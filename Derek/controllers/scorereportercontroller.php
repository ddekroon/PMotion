<?php

	class Controllers_ScoreReporterController extends Controllers_Controller {

		public function getMatchesInScoreReporter($team) : array {

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
		
		public function getScoreSubmissions(Models_Team $team, Models_Team $oppTeam, Models_Date $date): array {
			
			$scoreSubmissions = [];
			
			if($team == null || $date == null) {
				return $scoreSubmissions;
			}
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::scoreSubmissionsTable . " as scoreSubs "
					. "WHERE score_submission_date_id = " . $date->getId() . " "
					. "AND score_submission_team_id = " . $team->getId() . " ";
			
			if($oppTeam != null) {
				$sql .= "AND score_submission_opp_team_id = " . $oppTeam->getId() . " ";
			}
			
			$sql .= "AND score_submission_ignored = 0";
			
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

			if(!is_array($submissions) || count($submissions) == 0) {
				throw new Error("Internal error. Couldn't create submissions from the data given. Please contact the site admin if this issue persists.");
			}
			
			foreach($submissions as $submission) {
				$submission->validate();
			}

			//If we get here we have valid submissions, send emails and save.
			$this->sendScoreEmails($league, $team, $submissions); //Sends score reporter emails to admins, leave this before submitting scores
			
			foreach($submissions as $submission) {
				$submission->saveOrUpdate();
			}

			if (!$ignoreSubmission && !$league->getIsPlayoffs()) {
				//updateStandings($team, $submissions); //TODO
			} 
		}

		public function createSubmissionsFromRequest(Slim\Http\Request $request, Models_League $league, Models_Team $team, $ignoreSubmission) {
			
			$submissions = array();
			$allPostVars = $request->getParsedBody();

			$gameNum = 0;
			
			$oppTeamIds = $allPostVars['oppTeamID'];
			
			$resultValues = $allPostVars['result'];
			$scoreUsValues = isset($allPostVars['scoreUs']) ? $allPostVars['scoreUs'] : [];
			$scoreThemValues = isset($allPostVars['scoreThem']) ? $allPostVars['scoreThem'] : [];
			
			for ($i=0; $i < $league->getNumMatches(); $i++) {
								
				$spiritScore = new Models_SpiritScore();
				$spiritScore->setEditedValue(0);
				$spiritScore->setIsAdminAddition(false);
				$spiritScore->setIsDontShow(false);
				$spiritScore->setIsIgnored($ignoreSubmission);
				$spiritScore->setValue($allPostVars['spiritScore_' . $i]);
				
				$comment = new Models_ScoreSubmissionComment();
				$comment->setComment($allPostVars['matchComments_' . $i]);
				
				for ($j = 0; $j < $league->getNumGamesPerMatch(); $j++) {
					
					$gameSubmission = new Models_ScoreSubmission();
				
					$gameSubmission->setTeamId($team->getId());
					$gameSubmission->setOppTeamId($oppTeamIds[$i]);
					
					$gameSubmission->setSpiritScore($spiritScore);
					$gameSubmission->setScoreSubmissionComment($comment);
					
					$gameSubmission->setDateStamp(new DateTime());
					$gameSubmission->setIgnored($ignoreSubmission);
					$gameSubmission->setIsPhantom(false);
					$gameSubmission->setDontShow(false);
					
					$gameSubmission->setResult(is_array($resultValues) && count($resultValues) > $i ? $resultValues[$i] : 0);
					$gameSubmission->setScoreUs(is_array($scoreUsValues) && count($scoreUsValues) > $i ? $scoreUsValues[$i] : 0);
					$gameSubmission->setScoreThem(is_array($scoreThemValues) && count($scoreThemValues) > $i ? $scoreThemValues[$i] : 0);
					
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
			
			$commentAdded = false;
			$currDate = date('r');
			$firstSubmission = array_values($submissions)[0];
 
			$mailBody = '<table>'
				. "<tr><td>=======================================</td></tr>"
				. "<tr><td>Results mailed on $currDate</td></tr>"
				. "<tr><td>Team Name:      " . $team->getName() . "</td></tr>"
				. '<tr><td>Submitted by:   ' .  $firstSubmission->getSubmitterName() . ' (' . $firstSubmission->getSubmitterEmail() . ')</td></tr>'
				. "<tr><td>League:         " . $league->getDayString() . " " .$league->getName() . "</td></tr>"
				. "<tr><td>Date Played:    Week " . $firstSubmission->getDate()->getWeekNumber() . " - " . $firstSubmission->getDescription() . "</td></tr>"
				. "<tr><td>======================================</td></tr></table>";
			
			$mailBody.='<table align=left>';
			
			$scheduledMatches = $this->getMatchesInScoreReporter($team);
			$gameNum = 0;
			
			for ($i = 0; $i < $league->getNumMatches(); $i++) {
				$curSubmission = $submissions[$gameNum];
				
				for($j = 0; $j < $league->getNumGamesPerMatch(); $j++) {
					$gameNum++;
				}
				
				$teamsNotEqual = false; //By default assume they submitted for the correct team.
				$oppSubmissions = [];
				
				//Not playoffs, not against practice team, there is a scheduled match opp team
				if(!$league->getIsInPlayoffs() && $curSubmission->getOppTeamId() > 1 
						&& sizeof($scheduledMatches) > $i && $scheduledMatches[$i]->getOppTeamId($team) > 0) {
					
					if ($curSubmission->getOppTeamId() != $scheduledMatches[$i]->getOppTeamId($team)) {
						$teamsNotEqual = true;
					} else {
						$oppSubmissions = $this->getScoreSubmissions($curSubmission->getOppTeamId(), null, $curSubmission->getDate());
					}
				}

				$mailBody .= '<tr align="center"><td colspan=10><font color="#FF0000"><b>';
				
				$teamDBSubmissions = $this->getScoreSubmissions($team, null, $curSubmission->getDate());
				
				if (sizeof($teamDBSubmissions) > 0) {
					$mailBody .= "Results have already been submitted, these will be ignored<br />";
				}
				
				if(!$league->getIsInPlayoffs() && $curSubmission->getOppTeamId() > 0) {
					
					if ($teamsNotEqual) {
						$mailBody .= "Opponent submitted against is different than in the database<br />";
					} else {
						
						$oppSubmissionsCorrect = false;
						$curSubmissionAndOppSubmissionMatch = true; //1 signifies are the same
						
						for($k = 0; $k < sizeof($oppSubmissions); $k++) {
							if($oppSubmissions[$k]->getOppTeamId() == $team->getId()) {
								
								$oppSubmissionsCorrect = true;
								
								for($m = 0; $m < $games; $m++) {
									if (!checkSubmissionsMatch($submissions[($i * $games) + $m], $oppSubmissions[$k + $m])) {
										$curSubmissionAndOppSubmissionMatch = false;
									}
								}
								
								//$k = 1000000; //Could i not just use break; here?
							}
						}
						if (!$curSubmissionAndOppSubmissionMatch) {
							$mailBody.= "Submitted results don't match their opponenets<br />";
						} 
						if(!$oppSubmissionsCorrect && sizeof($oppSubmissions) > 0) {
							$mailBody.= "Opponents submitted against different teams<br />";
						}
					}
				}
				
				$mailBody .= '<br /></b></font>';
				$mailBody .= '</td></tr><tr><td align=center>';
				$mailBody .= "Submitted Results<br />";
				$mailBody .= "------------------------------------------<br />";
				$mailBody .= $this->printGameResults($submissions, $i * $league->getNumGamesPerMatch());
				$mailBody .= "------------------------------------------";

				$mailBody .= "</td><td width='10px'></td><td align=center>Opponent Results<br />";
				$mailBody .= "------------------------------------------<br />";
				
				if (sizeof($oppSubmissions) > 0) {
					for($k=0;$k<$matches*$games ; $k = $k+$games) {
						if($dbOppTeamOppID[$k] == $teamID) {
							$mailBody.= $this->printGameResults($sportID, $dbOppTeamOppID[$k], $dbOppTeamResult, $dbOppTeamScoreUs, $dbOppTeamScoreThem, $games, $k, $oppSpiritScore);
						}
					}			
				} else {
					$mailBody .= (!$league->getIsInPlayoffs() ? "Haven't submitted their scores yet<br />" : "Playoffs<br />");
				}
				$mailBody .= "------------------------------------------";

				$mailBody .= "</tr><tr><td colspan='3'>Comments: " 
						. ($curSubmission->getScoreSubmissionComment() != null ? $curSubmission->getScoreSubmissionComment()->getComment() : '')
						. "</td></tr>";

				if ($curSubmission->getScoreSubmissionComment() != null && strlen($curSubmission->getScoreSubmissionComment()) > 2) {
					$commentAdded = true;
				}
			}
			
			$mailBody.='</table>';

			if ($commentAdded) {
				$subject = "COMMENT - " . $league->getName() . " - " . $league->getDayString() . " - " . $team->getName();
			} else {
				$subject = $league->getName() . " - " . $league->getDayString() . " - " . $team->getName();
			}

			$mailBody = stripslashes($mailBody);
			
			$from_header  = 'MIME-Version: 1.0' . "\r\n";
			$from_header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$from_header .= 'Content-Transfer-Encoding: base64' . "\r\n";
			$from_header .= 'From: mail_form@perpetualmotion.org';
			
			foreach(Includes_WhoGetsEmailed::scoreSubmission() as $adminEmail) {
				mail($adminEmail, $subject, rtrim(chunk_split(base64_encode($mailBody))), $from_header);
			}

			return 1;
		}

		public function checkSubmissionsMatch(Integer $resultOne, Integer $resultTwo) {
			if ($resultOne == Includes_GameResults::WIN && $resultTwo != Includes_GameResults::LOSS
					|| $resultOne == Includes_GameResults::LOSS && $resultTwo != Includes_GameResults::WIN
					|| $resultOne == Includes_GameResults::TIE && $resultTwo != Includes_GameResults::TIE) {
				return false;
			}
			return true;
		}

		//Formats a teams submission for an email and returns the text
		public function printGameResults(array $submissions, Integer $matchNum) : String {
			
			if($submissions == null || sizeof($submissions) <= $matchNum) {
				return "";
			}
			
			$league = $submissions[0]->getTeam()->getLeague();
			
			$contents = "Opposition:    " + $submission->getOppTeam()->getName() + "<br />";
			
			for($i = 0; $i < $league->getNumGamesPerMatch(); $i++) {
				if(sizeof($submissions) <= $mathNum + $i) {
					continue; //No submission for this game. Not sure how this is possible but hey, good to error check.
				}
				
				$curSubmission = $submissions[$matchNum + $i];
								
				$contents .= 'Game ';
				$contents .= $i + 1 . ':      We ' + $curSubmission->getResultsString();
				
				if($league->getIsAskForScores() || $league->getIsInPlayoffs()) {
					$contents .= ' (Us:' . $curSubmission->getScoreUs() . ' Them: ' . $curSubmission->getScoreThem() . ')';
				}
				
				$contents.='<br />';
			}
			
			$spiritScoreVal = 0;
			$spiritScore = $submissions[$matchNum]->getSpiritScore();
			
			if($spiritScore != null) {
				$spiritScoreVal = $spiritScore->getValue();
			}
			
			if($spiritScoreVal == 0) {
				$spiritScoreString = 'N/A';
			} else {
				$spiritScoreString = number_format((float)$spiritScoreVal, 1, '.', '');
			}
			
			$contents .= '<br />Spirit Score ' . $spiritScoreString;
			$contents .= "<br /><br />";
			
			return $contents;	
		}
	}

?>