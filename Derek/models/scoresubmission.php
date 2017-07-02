<?php

class Models_ScoreSubmission extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $teamId;
	protected $oppTeamId;
	protected $dateId;
	protected $submitterName;
	protected $submitterEmail;
	protected $result;
	protected $scoreUs;
	protected $scoreThem;
	protected $ignored;
	protected $dateStamp;
	protected $dontShow;
	protected $isPhantom;
	
	private $spiritScore;
	private $scoreSubmissionComment;
	private $team;
	private $oppTeam;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::scoreSubmissionsTable . " WHERE score_submission_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
        // no id if we're creating
        if(isset($data['score_submission_id'])) {
            $this->id = $data['score_submission_id'];
        }
		
        $this->teamId = $data['score_submission_team_id'];
        $this->oppTeamId = $data['score_submission_opp_team_id'];
        $this->dateId = $data['score_submission_date_id'];
        $this->submitterName = $data['score_submission_submitter_name'];
        $this->submitterEmail = $data['score_submission_submitter_email'];
        $this->result = $data['score_submission_result'];
        $this->scoreUs = $data['score_submission_score_us'];
        $this->scoreThem = $data['score_submission_score_them'];
        $this->ignored = $data['score_submission_ignored'];
        $this->dateStamp = $data['score_submission_datestamp'];
        $this->dontShow = $data['score_submission_dont_show'];
        $this->isPhantom = $data['score_submission_is_phantom'];
    }
	
	public function getSpiritScore() {
		if($this->spiritScore == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::spiritScoresTable . " WHERE spirit_score_score_submission_id = " . $this->getId() . " LIMIT 1";

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$spiritScore = new Models_SpiritScore();
				$spiritScore->fill($row);
				$spiritScore->setScoreSubmission($this);
				$this->spiritScore = $spiritScore;
			}
		}
		
		return $this->spiritScore;
	}
	
	public function setSpiritScore($spiritScore) {
		$this->spiritScore = $spiritScore;
	}

	public function getScoreSubmissionComment() {
		
		if($this->scoreSubmissionComment == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::scoreCommentsTable . " WHERE comment_score_submission_id = " . $this->getId() . " LIMIT 1";

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$comment = new Models_ScoreSubmissionComment();
				$comment->fill($row);
				$comment->setScoreSubmission($this);
				$this->scoreSubmissionComment = $comment;
			}
		}
		
		return $this->scoreSubmissionComment;
	}

	public function setScoreSubmissionComment($scoreSubmissionComment) {
		$this->scoreSubmissionComment = $scoreSubmissionComment;
	}

	function getTeam() {
		if($this->team == null && $this->db != null && $this->getTeamId() != null) {
			$this->team = Models_League::withID($this->db, $this->logger, $this->getTeamId());
		}
		
		return $this->team;
	}
	
	function getOppTeam() {
		if($this->oppTeam == null && $this->db != null && $this->getOppTeamId() != null) {
			$this->oppTeam = Models_Team::withID($this->db, $this->logger, $this->getOppTeamId());
		}
		
		return $this->oppTeam;
	}
	
	public function getResultsString() {
		switch($this->getResult()) {
			case Includes_GameResults::WIN:
				return "Won";
			case Includes_GameResults::LOSS:
				return "Lost";
			case Includes_GameResults::TIED:
				return "Tied";
			case Includes_GameResults::CANCELLED:
				return "Cancelled";
			case Includes_GameResults::PRACTICE:
				return "Practiced";
			case Includes_GameResults::ERROR:
				return "*";
			default:
				return "Error";
		}
	}
	
    public function getId() {
        return $this->id;
    }
	
	public function getDateId() {
		return $this->dateId;
	}

	public function getOppTeamId() {
		return $this->oppTeamId;
	}
	
	public function setOppTeamId($oppTeamId) {
		$this->oppTeamId = $oppTeamId;
	}
	
	function getTeamId() {
		return $this->teamId;
	}

	function getSubmitterName() {
		return $this->submitterName;
	}

	function getSubmitterEmail() {
		return $this->submitterEmail;
	}

	function getResult() {
		return $this->result;
	}

	function getScoreUs() {
		return $this->scoreUs;
	}

	function getScoreThem() {
		return $this->scoreThem;
	}

	function getIgnored() {
		return $this->ignored;
	}

	function getDateStamp() {
		return $this->dateStamp;
	}

	function getDontShow() {
		return $this->dontShow;
	}

	function getIsPhantom() {
		return $this->isPhantom;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setSubmitterName($submitterName) {
		$this->submitterName = $submitterName;
	}

	function setSubmitterEmail($submitterEmail) {
		$this->submitterEmail = $submitterEmail;
	}

	function setResult($result) {
		$this->result = $result;
	}

	function setScoreUs($scoreUs) {
		$this->scoreUs = $scoreUs;
	}

	function setScoreThem($scoreThem) {
		$this->scoreThem = $scoreThem;
	}

	function setIgnored($ignored) {
		$this->ignored = $ignored;
	}

	function setDateStamp($dateStamp) {
		$this->dateStamp = $dateStamp;
	}

	function setDontShow($dontShow) {
		$this->dontShow = $dontShow;
	}

	function setIsPhantom($isPhantom) {
		$this->isPhantom = $isPhantom;
	}
	
	function validate(League $league) {
		
		$gameNum = 0; 
		$matchNum = 0;
		
		if ($league == null) {
			throw new Exception('No League Given');
		}
		
		if ($this->teamId == null || $this->teamId <= 1) { //Team 1 is practice. Can't submit a score for them.
			throw new Exception('Invalid Team');
		}

		if($league->getIsPlayoffs() == 0) {

			for ($i=0;$i<$matches;$i++) {
				$matchNum++;
				if ($oppTeamID[$i] == 0) {
					$error.='* Please enter a team for match '.$matchNum.'<br />';
				}
				if ($spiritScores[$i] && $spiritScores[$i] < 3.5 && strlen($matchComments[$i]) < 2 && $oppTeamID[$i] != 1) {
					$error.='* Please enter the reason for spirit being so low in match '.$matchNum.'<br />';
				}
				if ($oppTeamID[$i] == $teamID) {
					$error.='* Please select a different opposing team for game '.$matchNum.'<br />';
				}
				if ($oppTeamID[$i] > 2) {
					for ($j=0;$j<$games;$j++) {
						if (!$gameResults[$gameNum]) {
							$error.='* Please enter a result for game '.$gameNum.'<br />';
						}
						$gameNum++;
					}
				}
			}

			if ($matches == 2) {
				if ($oppTeamID[0] == $oppTeamID[1]) {
					$error.='* Please select two different opposing teams<br />';
				}
			}
		}

		if (strlen($submitName) < 2) {
			$error.='* Please enter your name<br />';
		} else {
			if (!isValid($submitName)) {
				$error.='* Please enter a valid name<br />';
			}
		}

		return $error;
	}
	
	function saveOrUpdate() {
		if($this->getId() == null) {
			save();
		} else {
			update();
		}
	}
	
	function save() {
		global $scoreSubmissionsTable, $leaguesTable, $teamsTable, $scoreCommentsTable, $seasonsTable, $spiritScoresTable;
		global $teamID, $leagueID, $dateID, $actualWeekDate, $dayOfYear, $isPlayoffs;
		global $oppTeamID, $scoreUs, $scoreThem, $gameResults, $spiritScores, $matchComments, $submitName, $submitEmail, $matches, $games;

		$ignored = checkSubmitWeek($teamID, $dateID); //0 for hasnt, 1 for has
		updateCaptain($teamID, $submitName, $submitEmail);
		$gameNum = 0;

		$submissionArray = query("SELECT MAX(score_submission_id) as maxnum FROM $scoreSubmissionsTable");
		$newSubmissionNum = $submissionArray['maxnum'];

		$escapedName = mysql_real_escape_string($submitName);
		$escapedEmail = mysql_real_escape_string($submitEmail);

		//submits teamsTable update first in case teams refresh half way through. Emails get sent to admins first so we know 
		//scores that were attempted to be submitted
		$submissionEntered=mysql_query("UPDATE $teamsTable INNER JOIN $leaguesTable ON $teamsTable.team_league_id = 
			$leaguesTable.league_id SET team_most_recent_week_submitted = league_week_in_score_reporter WHERE team_id = $teamID") 
			or die('ERROR updating teamsTable - '.mysql_error()); //sets the week submitted for the team to current

		//Inserts data into scores comments, score submissions, and spirit scores database. SS and comments are connected to 
		//second game of beach volleyball score submissions. New score submission made for each game.
		for($i=0;$i<$matches;$i++) {
			$escapedComments[$i] = mysql_real_escape_string($matchComments[$i]);
			for($j=0;$j<$games;$j++) {
				if ($oppTeamID[$i] == 1) {
					$gameResults[$gameNum] = 5;
				}
				$newSubmissionNum++;
				mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, 
					score_submission_opp_team_id, score_submission_date_id, score_submission_submitter_name, 
					score_submission_submitter_email, score_submission_result, score_submission_score_us, score_submission_score_them,
					score_submission_ignored, score_submission_datestamp) VALUES ($newSubmissionNum, $teamID, $oppTeamID[$i], $dateID,
					'$escapedName', '$escapedEmail', $gameResults[$gameNum], $scoreUs[$gameNum], $scoreThem[$gameNum], $ignored, 
					NOW())") or die ('Error with inserting into score submission db - '.mysql_error());
				if ($j == 0) { //connects spirit submission and comments with the first score submission
					if($spiritScores[$i] != 0 && $gameResults[$gameNum]!=4) {
						mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, 
							spirit_score_ignored, spirit_score_edited_value) VALUES ($newSubmissionNum, $spiritScores[$i], $ignored, 
							$spiritScores[$i])") or die('spirit score insert - '.mysql_error());
					}
					if (strlen($matchComments[$i]) > 2) {
						mysql_query("INSERT INTO $scoreCommentsTable (comment_score_submission_id, comment_value) VALUES
							($newSubmissionNum, '$escapedComments[$i]')") or die('comments insert - '.mysql_error());
					}	
				}
				$gameNum++;
			}
		}
		return $ignored;
	}
	
	function update() {
		
	}
}
