<?php

	class Controllers_ScoreReporterController extends Controllers_Controller {

		private $templateEngine;
		
		public function __construct($db, $logger) {
			parent::__construct($db, $logger);
			$this->templateEngine = new League\Plates\Engine(TEMPLATES_PATH);
		}
		
		public function getMatchesInScoreReporter($team) {

			if(isset($team) && $team->getId() != null && $team->getLeague() !== null && $team->getLeague()->getId() != null) {
				$sql = "SELECT " . Includes_DBTableNames::scheduledMatchesTable . ".* FROM " . Includes_DBTableNames::scheduledMatchesTable . " "
						. "INNER JOIN " . Includes_DBTableNames::datesTable . " ON " . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_date_id = " . Includes_DBTableNames::datesTable . ".date_id "
						. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " ON " . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_league_id = " . Includes_DBTableNames::leaguesTable . ".league_id "
						. "WHERE (" . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_team_id_2 = " . $team->getId() . " OR " . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_team_id_1 = " . $team->getId() . ") "
						. "AND " . Includes_DBTableNames::datesTable . ".date_week_number = " . Includes_DBTableNames::leaguesTable . ".league_week_in_score_reporter "
						. "AND " . Includes_DBTableNames::datesTable . ".date_season_id = " . Includes_DBTableNames::leaguesTable . ".league_season_id AND " . Includes_DBTableNames::leaguesTable . ".league_id = " . $team->getLeagueId() . " "
						. "ORDER BY scheduled_match_time";

				$stmt = $this->db->query($sql);
				
				$matches = [];

				//goes through the results and figures out which opponents team teamID had
				for($i = 0; $i < $team->getLeague()->getNumMatches(); $i++) {

					$matchNode = $stmt->fetch();
					
					//This if statements checks if both scheduled matches are against the same team, this can happen if you double submit a leagues matches for a week.
					if($i != 0) {
						if(($matchNode['scheduled_match_team_id_1'] == $matches[$i-1]->getOppTeamId($team) || $matchNode['scheduled_match_team_id_2'] == $matches[$i-1]->getOppTeamId($team)) 
								&& $matchNode['scheduled_match_team_id_1'] != '') {
							$i--;
							continue;
						}
					}
					
					if($matchNode == false) {
						//echo "No matches scheduled this week";
						//break;
						$match = new Models_ScheduledMatch();
					} else {
						$match = Models_ScheduledMatch::withRow($this->db, $this->logger, $matchNode);
					}

					/* if ($matchNode['scheduled_match_team_id_1'] == $team->getId()) {
						$match->setOppTeamId($matchNode['scheduled_match_team_id_2']);
					} else {
						$match->setOppTeamId($matchNode['scheduled_match_team_id_1']);
					} */

					$matches[] = $match;
				}

				return $matches;
			}

			return [];
		}
		
		public function getScoreSubmissions(Models_Team $team, Models_Team $oppTeam = null, Models_Date $date) {
			
			$scoreSubmissions = [];
			
			if($team == null || $date == null) {
				return $scoreSubmissions;
			}
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::scoreSubmissionsTable . " as scoreSubs "
					. "WHERE scoreSubs.score_submission_date_id = " . $date->getId() . " "
					. "AND scoreSubs.score_submission_team_id = " . $team->getId() . " ";
			
			if($oppTeam != null) {
				$sql .= "AND scoreSubs.score_submission_opp_team_id = " . $oppTeam->getId() . " ";
			}
			
			$sql .= "AND scoreSubs.score_submission_ignored = 0";
						
			$stmt = $this->db->query($sql);

			//goes through the results and figures out which opponents team teamID had
			while(($row = $stmt->fetch()) != false) {
				$scoreSubmissions[] = Models_ScoreSubmission::withRow($this->db, $this->logger, $row);
			}

			return $scoreSubmissions;
		}

		public function saveFromRequest(Slim\Http\Request $request) {
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			
			$allPostVars = $request->getParsedBody();
			$leagueID = $allPostVars['leagueID'];
			$teamID = $allPostVars['teamID'];
			
			$league = Models_League::withID($this->db, $this->logger, $leagueID);
			$team = Models_Team::withID($this->db, $this->logger, $teamID);
			
			$activeDate = null;
			
			if(isset($allPostVars['dateID']) && $allPostVars['dateID'] > 0) {
				$activeDate = Models_Date::withID($this->db, $this->logger, $allPostVars['dateID']);
			} else {
				$activeDate = $leaguesController->getActiveDate($league);
			}
			
			$ignoreSubmission = $this->checkSubmissionExists($team, $activeDate);
			
			$submissions = $this->createSubmissionsFromRequest($request, $league, $team, $ignoreSubmission);

			$this->saveSubmissions($submissions, $league, $team, $ignoreSubmission);
		}

		function saveSubmissions($submissions, $league, $team, $ignoreSubmission) {
			
			if(!is_array($submissions) || count($submissions) == 0) {
				throw new Error("Internal error. Couldn't create submissions from the data given. Please contact the site admin if this issue persists.");
			}
			
			$matchNum = 1;
			foreach($submissions as $submission) {
				$submission->validate($league, $matchNum++);
			}

			//If we get here we have valid submissions, send emails and save.
			$this->sendScoreEmails($league, $team, $submissions); //Sends score reporter emails to admins, leave this before submitting scores
			
			foreach($submissions as $submission) {
				$submission->saveOrUpdate();
			}

			if (!$ignoreSubmission) {
				
				$team->setMostRecentWeekSubmitted($league->getWeekInScoreReporter());
				$team->update();
				
				if(!$league->getIsInPlayoffs()) {
					$this->updateStandingsFromSubmissions($team, $submissions);
				}
			} 
		}

		public function createSubmissionsFromRequest(Slim\Http\Request $request, Models_League $league, Models_Team $team, $ignoreSubmission) {
			
			$submissions = array();
			$allPostVars = $request->getParsedBody();

			$gameNum = 0;
			
			for ($i=0; $i < $league->getNumMatches(); $i++) {
								
				$spiritScore = new Models_SpiritScore();
				$spiritScore->setDb($this->db);
				$spiritScore->setLogger($this->logger);
				$spiritScore->setEditedValue($allPostVars['spiritScore_' . $i]);
				$spiritScore->setIsAdminAddition(false);
				$spiritScore->setIsDontShow(false);
				$spiritScore->setIsIgnored($ignoreSubmission);
				$spiritScore->setValue($allPostVars['spiritScore_' . $i]);
				
				$comment = new Models_ScoreSubmissionComment();
				$comment->setDb($this->db);
				$comment->setLogger($this->logger);
				$comment->setComment($allPostVars['matchComments_' . $i]);
				
				$oppTeamId = $allPostVars['oppTeamID_' . $i];
				
				for ($j = 0; $j < $league->getNumGamesPerMatch(); $j++) {
					
					$gameSubmission = new Models_ScoreSubmission();
					$gameSubmission->setDb($this->db);
					$gameSubmission->setLogger($this->logger);
				
					$gameSubmission->setDate($league->getDateInScoreReporter());
					$gameSubmission->setTeamId($team->getId());
					$gameSubmission->setOppTeamId($oppTeamId);
					
					if($j == 0) { //Only attach spirit, comment to the first score submission. Stupid system but legacy and I don't want to change it.
						$gameSubmission->setSpiritScore($spiritScore);
						
						if(strlen($comment->getComment()) > 2) {
							$gameSubmission->setScoreSubmissionComment($comment);
						}
					}
					
					$gameSubmission->setDateStamp(new DateTime());
					$gameSubmission->setIsIgnored($ignoreSubmission);
					$gameSubmission->setIsPhantom(false);
					$gameSubmission->setIsDontShow(false);
										
					$gameSubmission->setResult(isset($allPostVars['result_' . $i . '_' . $j]) ? $allPostVars['result_' . $i . '_' . $j] : Includes_GameResults::ERROR);
					
					if($league->getIsAskForScores()) {
						$gameSubmission->setScoreUs(isset($allPostVars['scoreUs_' . $i . '_' . $j]) ? $allPostVars['scoreUs_' . $i . '_' . $j] : 0);
						$gameSubmission->setScoreThem(isset($allPostVars['scoreThem_' . $i . '_' . $j]) ? $allPostVars['scoreThem_' . $i . '_' . $j] : 0);
					} else {
						$gameSubmission->setScoreUs(0);
						$gameSubmission->setScoreThem(0);
					}
					
					$gameSubmission->setSubmitterName($allPostVars['submitterName']);
					$gameSubmission->setSubmitterEmail($allPostVars['submitterEmail']);					

					$submissions[] = $gameSubmission;
					
					$gameNum++;
				}
			}
			
			
			return $submissions;
		}

		/**
		 * Checks if the given team has already submitted a score for the given date
		 * @param Models_Team $team
		 * @param Models_Date $date
		 * @return true if they've already submitted, false otherwise.
		 */
		public function checkSubmissionExists(Models_Team $team, Models_Date $date) {
			$sql = "SELECT count(*) FROM " . Includes_DBTableNames::scoreSubmissionsTable
				. " WHERE score_submission_team_id = " . $team->getId() . " AND score_submission_date_id = " . $date->getId() . " AND score_submission_ignored = 0";

			$stmt = $this->db->query($sql);

			return $stmt->fetchColumn() > 0; //this is so stupid but needed to not break when sending the boolean through to another typed method.
		}


		//Checks if a team submitted against the right team compared to whats in the database scheduled_matches. If that's wrong, sends that, the scores they sent, 
		//the scores the team they said they played against submitted, and the scores the team they were supposed to play submitted.
		//Next it checks if what they submitted was different than what their opponent did. SPAMS ZACH! :D
		function sendScoreEmails(Models_League $league, Models_Team $team, array $submissions) {
			
			if($league == null || $team == null || $submissions == null || count($submissions) == 0) {
				return;
			}
			
			$emailController = new Controllers_EmailsController($this->db, $this->logger);
			$emailTemplate = Includes_EmailTypes::scoreSubmission();
			
			$commentAdded = false;
			$gameNum = 0;
			
			for ($i = 0; $i < $league->getNumMatches(); $i++) {
				$curSubmission = $submissions[$gameNum];
				$gameNum = $gameNum + $league->getNumGamesPerMatch();
				
				if ($curSubmission->getScoreSubmissionComment() != null && strlen($curSubmission->getScoreSubmissionComment()) > 2) {
					$commentAdded = true;
				}
			}
			
			$subject = ($commentAdded ? "COMMENT - " : "") . $league->getName() . " - " . $league->getDayString() . " - " . $team->getName();
			
			$body = $this->templateEngine->render('email-score-submission', [
				"team" => $team,
				"league" => $league,
				"submissions" => $submissions,
				"firstSubmission" => array_values($submissions)[0],
				"currDate" => date('F j, Y g:ia'),
				"scheduledMatches" => $this->getMatchesInScoreReporter($team),
				"scoreReporterController" => $this,
				"matches" => $league->getNumMatches(),
				"games" => $league->getNumGamesPerMatch()
			]);

			$emailController->createAndSendEmail(
					$emailTemplate->getEmailType(), 
					$subject, 
					$body, 
					implode(",", $emailTemplate->getToAddresses()), 
					$emailTemplate->getFromName(),
					$emailTemplate->getFromAddress(), 
					null, 
					null
			);

			return 1;
		}

		public function checkSubmissionsMatch($resultOne, $resultTwo) {
			if ($resultOne == Includes_GameResults::WIN && $resultTwo != Includes_GameResults::LOSS
					|| $resultOne == Includes_GameResults::LOSS && $resultTwo != Includes_GameResults::WIN
					|| $resultOne == Includes_GameResults::TIE && $resultTwo != Includes_GameResults::TIE) {
				return false;
			}
			return true;
		}
		
		public function updateStandingsFromSubmissions(Models_Team $team, array $scoreSubmissions) {
			foreach($scoreSubmissions as $submission) {
				if ($submission->getResult() == Includes_GameResults::WIN) {
					$team->setWins($team->getWins() + 1);
				} else if ($submission->getResult() == Includes_GameResults::LOSS) {
					$team->setLosses($team->getLosses() + 1);
				} else if ($submission->getResult() == Includes_GameResults::TIE) {
					$team->setTies($team->getTies() + 1);
				}
			}
			
			$team->update();
		}

		public function parseScoreSubmissionFromJson($scoreSubmissionJson, $league, $team, $ignoreSubmission, $activeDate) {
			
			$submissions = array();

			$gameNum = 0;
			
			for ($i = 0; $i < $league->getNumMatches(); $i++) {
				$curMatch = $scoreSubmissionJson['matches'][$i];
								
				$spiritScore = new Models_SpiritScore();
				$spiritScore->setDb($this->db);
				$spiritScore->setLogger($this->logger);
				$spiritScore->setEditedValue($curMatch['spiritScore']);
				$spiritScore->setIsAdminAddition(false);
				$spiritScore->setIsDontShow(false);
				$spiritScore->setIsIgnored($ignoreSubmission);
				$spiritScore->setValue($curMatch['spiritScore']);
				
				$comment = new Models_ScoreSubmissionComment();
				$comment->setDb($this->db);
				$comment->setLogger($this->logger);
				$comment->setComment($curMatch['comment']);
				
				$oppTeamId = $curMatch['oppTeamId'];
				
				for ($j = 0; $j < $league->getNumGamesPerMatch(); $j++) {
					$curGame = $curMatch['results'][$j];
					
					$gameSubmission = new Models_ScoreSubmission();
					$gameSubmission->setDb($this->db);
					$gameSubmission->setLogger($this->logger);
				
					$gameSubmission->setDate($activeDate);
					$gameSubmission->setTeamId($team->getId());
					$gameSubmission->setOppTeamId($oppTeamId);
					
					if($j == 0) { //Only attach spirit, comment to the first score submission. Stupid system but legacy and I don't want to change it.
						$gameSubmission->setSpiritScore($spiritScore);
						
						if(strlen($comment->getComment()) > 2) {
							$gameSubmission->setScoreSubmissionComment($comment);
						}
					}
					
					$gameSubmission->setDateStamp(new DateTime());
					$gameSubmission->setIsIgnored($ignoreSubmission);
					$gameSubmission->setIsPhantom(false);
					$gameSubmission->setIsDontShow(false);
										
					$gameSubmission->setResult($curGame['result']);
					
					if($league->getIsAskForScores()) {
						$gameSubmission->setScoreUs($curGame['scoreUs']);
						$gameSubmission->setScoreThem($curGame['scoreThem']);
					} else {
						$gameSubmission->setScoreUs(0);
						$gameSubmission->setScoreThem(0);
					}
					
					$gameSubmission->setSubmitterName($scoreSubmissionJson['name']);
					$gameSubmission->setSubmitterEmail($scoreSubmissionJson['email']);					

					$submissions[] = $gameSubmission;
					
					$gameNum++;
				}
			}
				
			return $submissions;
		}
	}

?>