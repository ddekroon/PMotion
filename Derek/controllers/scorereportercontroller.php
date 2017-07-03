<?php

	class Controllers_ScoreReporterController extends Controllers_Controller {

		public function getMatches($team) {

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
						if(($matchNode['scheduled_match_team_id_1'] == $matches[$i-1]->getOppTeamId() || $matchNode['scheduled_match_team_id_2'] == $matches[$i-1]->getOppTeamId()) 
								&& $matchNode['scheduled_match_team_id_1'] != '') {
							$i--;
							continue;
						}
					}
					
					if($matchNode == false) {
						//echo "No matches scheduled this week";
						//break;
						$match = new Models_Match();
					} else {
						$match = Models_Match::withRow($this->db, $this->logger, $matchNode);
					}

					if ($matchNode['scheduled_match_team_id_1'] == $team->getId()) {
						$match->setOppTeamId($matchNode['scheduled_match_team_id_2']);
					} else {
						$match->setOppTeamId($matchNode['scheduled_match_team_id_1']);
					}

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
			$this->sendScoreEmails($league, $team, $submissions); //TODO: sends score reporter emails to admins, leave this before submitting scores
			
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
			
			$teamsNotEqual = 0;
			$oppName = array();
			$dbOppName = array();
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
			
			$scheduledMatches = $this->getMatches($team);
			$gameNum = 0;
			
			for ($i = 0; $i < $league->getNumMatches(); $i++) {
				$curSubmission = $submissions[$gameNum];
				
				if($isPlayoffs == 0 && $scheduledMatches[0]->getOppTeamId() > 1) {
					$oppSubmissions = $this->getScoreSubmissions($this->getOppTeamId(), $this-> $firstSubmission->getDate())
					
					$oppSpiritArray = mysql_fetch_array($oppSpiritQuery);
					$oppSpiritScore = $oppSpiritArray['spirit_score_value'];

					$teamsNotEqual = 0;
					$dbOppSubmittedScore = 1; //by default, assumes the opponent has submitted their score
					$oppSubmittedScore = 1;
					if($dbOppTeamID[$i] > 0) {
						if ($dbOppTeamID[$i] != $oppTeamID[$i]) {
							$teamsNotEqual = 1;
						}
						$dbOppSubmissionQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable 
							Inner Join $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
							Inner Join $leaguesTable ON $datesTable.date_day_number = $leaguesTable.league_day_number 
							WHERE score_submission_team_id = $dbOppTeamID[$i] 
							AND $datesTable.date_week_number = $leaguesTable.league_week_in_score_reporter 
							AND $leaguesTable.league_id = $leagueID AND score_submission_ignored = 0") 
							or die('ERROR - Getting score submissions data for database team '. mysql_error());
						if (mysql_num_rows($dbOppSubmissionQuery) >0) {
							$numDBOppScores = 0;
							while($dbOppSubmission = mysql_fetch_array($dbOppSubmissionQuery)) {
								$dbOppTeamOppID[$numDBOppScores] = $dbOppSubmission['score_submission_opp_team_id'];
								$dbOppTeamResult[$numDBOppScores] = $dbOppSubmission['score_submission_result'];
								$dbOppTeamScoreUs[$numDBOppScores] = $dbOppSubmission['score_submission_score_us'];
								$dbOppTeamScoreThem[$numDBOppScores] = $dbOppSubmission['score_submission_score_them'];
								$numDBOppScores++;			
							}
							if ($numDBOppScores > $games*$matches) {
								$mailBody.='<font color="#FF0000"><b>Opponent has more than 4 unignored scores in the score_submissions database</b></font>';
							}
						} else {
							$numDBOppScores = 0;
							$dbOppSubmittedScore = 0;
						}
					} else { //Either in the playoffs or first team was never obtained, opponent never submitted their score
						$numDBOppScores = 0;
						$dbOppSubmittedScore = 0;
					}
				}

				$mailBody.='<tr align="center"><td colspan=10><font color="#FF0000"><b>';
				//At this point there's one or two arrays storing all the opponents submittion data... hopefully, this of course gets repeated for each match
				$matchQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable 
					Inner Join $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
					Inner Join $leaguesTable ON $datesTable.date_day_number = $leaguesTable.league_day_number 
					WHERE score_submission_team_id = $teamID AND $datesTable.date_week_number = $leaguesTable.league_week_in_score_reporter 
					AND $leaguesTable.league_id = $leagueID AND score_submission_ignored = 0") or die(mysql_error());
				if (mysql_num_rows($matchQuery) > 0) {
					$mailBody.="Results have already been submitted, these will be ignored<br />";
				}
				if($isPlayoffs == 0 && $dpOppTeamID[0] != 0) {
					if ($teamsNotEqual == 1) {
						$mailBody.= "Opponent submitted against is different than in the database<br />";
					} else {
						$oppSubmissionsCorrect = 0;
						$scoresChecker = 1; //1 signifies are the same
						for($k=0;$k<$numDBOppScores;$k++) {
							if($dbOppTeamOppID[$k] == $teamID) {
								$oppSubmissionsCorrect = 1;
								for($m=0;$m<$games;$m++) {
									if (checkGameEqual($gameResults[$i*$games+$m], $dbOppTeamResult[$k+$m]) == false) {
										$scoresChecker = 0;
									}
								}
								$k=1000;
							}
						}
						if ($scoresChecker == 0 && $oppSubmissionsCorrect == 1 && $numDBOppScores > 0) {
							$mailBody.= "Submitted results don't match their opponenets<br />";
						} 
						if($oppSubmissionsCorrect == 0 && $numDBOppScores > 0) {
							$mailBody.= "Opponents submitted against different teams<br />";
						}
					}
				}
				$mailBody.='<br /></b></font>';
				$mailBody.='</td></tr><tr><td align=center>';
				$mailBody.= "Submitted Results<br />";
				$mailBody.="------------------------------------------<br />";
				$mailBody.= formatResults($sportID, $oppTeamID[$i], $gameResults, $scoreUs, $scoreThem, $games, $i*$games, $spiritScores[$i]);
				$mailBody.="------------------------------------------";

				$mailBody.= "<td width='10px'><br /></td><td align=center>Opponent Results<br />";
				$mailBody.="------------------------------------------<br />";
				if ($dbOppSubmittedScore == 1) {
					for($k=0;$k<$matches*$games ; $k = $k+$games) {
						if($dbOppTeamOppID[$k] == $teamID) {
							$mailBody.= formatResults($sportID, $dbOppTeamOppID[$k], $dbOppTeamResult, $dbOppTeamScoreUs, $dbOppTeamScoreThem, $games, $k, $oppSpiritScore);
						}
					}			
				} else {
					$isPlayoffs == 0?$mailBody.= "Haven't submitted their scores yet<br />":$mailBody.= "Playoffs<br />";
				}
				$mailBody.="------------------------------------------";

				$mailBody.= "</tr><tr><td colspan=3+$teamsNotEqual*2>comments: $matchComments[$i]<br /><br /></td></tr>";

				if (strlen($matchComments[$i]) > 2) {
					$commentAdded = 1;
				}
			}
			$mailBody.='</table>';

			if ($commentAdded == 1) {
				$subject = "COMMENT - $leagueName - ".dayOfWeek($dayNumber)." - $teamName";
			} else {
				$subject = "$leagueName - ".dayOfWeek($dayNumber)." - $teamName";
			}

			$mailBody=stripslashes($mailBody);
			$from_header  = 'MIME-Version: 1.0' . "\r\n";
			$from_header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$from_header .= 'Content-Transfer-Encoding: base64' . "\r\n";
			$from_header .= 'From: mail_form@perpetualmotion.org';

			$toMailArray = scoreSubmission();
			foreach($toMailArray as $adminEmail) {
				mail($adminEmail, $subject, rtrim(chunk_split(base64_encode($mailBody))), $from_header);
			}

			return 1;
		}

		//Checks if the results the person submitted match up with what their opponent submitted
		public function checkResultsEqual($games, $gameResults, $oppTeamResult, $curMatch) {
			for ($j = 0;$j < $games; $j++) {
				if ($gameResults[$curMatch*$games + $j] ==1 && $oppTeamResult[$j] != 2) {
					return false;
				} elseif ($gameResults[$curMatch*$games + $j] ==2 && $oppTeamResult[$j] != 1) {
					return false;
				} elseif ($gameResults[$curMatch*$games + $j] ==3 && $oppTeamResult[$j] != 3) {
					return false;
				}
			}
			return true;
		}

		public function checkGameEqual($gameResults, $oppTeamResult) {
			if ($gameResults ==1 && $oppTeamResult != 2) {
				return false;
			} elseif ($gameResults ==2 && $oppTeamResult != 1) {
				return false;
			} elseif ($gameResults ==3 && $oppTeamResult != 3) {
				return false;
			}
			return true;
		}

		//Formats a teams submission for an email and returns the text
		public function formatResults($sportID, $teamOppID, $teamResult, $teamScoreUs, $teamScoreThem, $games, $curMatch, $spiritScore) {
			global $teamsTable, $isPlayoffs;
			$teamArray = query("SELECT * FROM $teamsTable WHERE team_id = $teamOppID");
			$teamName = $teamArray['team_name'];
			$contents = '';

			$contents .= "Opposition:    $teamName<br />";
			for($i = 0;$i < $games; $i++){
				if ($teamResult[$curMatch + $i] == 1) {
					$winLossTie = 'We Won';
				} else if ($teamResult[$curMatch + $i] == 2) {
					$winLossTie = 'We Lost';
				} else if ($teamResult[$curMatch + $i] == 3) {
					$winLossTie = 'We Tied';
				} else if ($teamResult[$curMatch + $i] == 4) {
					$winLossTie = 'Cancelled';
				} else if ($teamResult[$curMatch + $i] == 5) {
					$winLossTie = 'Practise';
				} else if ($teamResult[$curMatch + $i] == 0) {
					$winLostTie = 'No Game';
				} else {
					$winLossTie = 'Error -'.$teamResult[$curMatch + $i].'-';
				}
				$contents .= 'Game ';
				$contents .= $i+1 .':      '.$winLossTie;
				if($sportID != 2 || $isPlayoffs == 1) {
					$contents .= ' (Us:' . $teamScoreUs[$curMatch + $i] .' Them:' .$teamScoreThem[$curMatch + $i] . ")";
				}
				$contents.='<br />';
			}
			$spiritScoreString = number_format((float)$spiritScore, 1, '.', '');
			if($spiritScoreString == 0) {
				$spiritScoreString = 'N/A';
			}
			$contents.='<br />Spirit Score '.$spiritScoreString;
			$contents .= "<br /><br />";
			return $contents;	
		}

		//emails helper, figures out the actual week day
		public function dayOfWeek($dayNumber) {
			if ($dayNumber == 1) {
				return 'Monday';
			} else if ($dayNumber == 2) {
				return 'Tuesday';
			} else if ($dayNumber == 3) {
				return 'Wednesday';
			} else if ($dayNumber == 4) {
				return 'Thursday';
			} else if ($dayNumber == 5) {
				return 'Friday';
			} else if ($dayNumber == 6) {
				return 'Saturday';
			} else if ($dayNumber == 7) {
				return 'Sunday';
			}
		}
	}

?>