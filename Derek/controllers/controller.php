<?php

abstract class Controllers_Controller {
    protected $db;
	protected $logger;
	
	protected $addressesTable = 'addressdatabase';
	protected $authTable = 'auth_dbtable';
	protected $captainsTable = 'captains_dbtable';	
	protected $datesTable = 'dates_dbtable';
	protected $faqTable = 'faq_dbtable';
	protected $fieldsTable = 'fields_dbtable';
	protected $gameSlotsTable = 'game_slots_dbtable';
	protected $individualsTable = 'individuals_dbtable';
	protected $leaguesTable = 'leagues_dbtable';
	protected $playersTable = 'players_dbtable';
	protected $prizesTable = 'prizes';
	protected $registrationCommentsTable = 'registration_comments_dbtable';
	protected $scheduledMatchesTable = 'scheduled_matches_dbtable';
	protected $scheduleVariablesTable = 'schedule_variables_dbtable';
	protected $scoreCommentsTable = 'score_comments_dbtable';
	protected $scoreSubmissionsTable = 'score_submissions_dbtable';
	protected $seasonsTable = 'seasons_dbtable';
	protected $spiritScoresTable = 'spirit_scores_dbtable';
	protected $sportsTable = 'sports_dbtable';
	protected $standingsCommentsTable = 'standings_comments_dbtable';
	protected $surveysTable = 'surveys';
	protected $teamPictureArchivesTable = 'teampicturearchive'; //NOTE i dont think i ever used this one
	protected $teamsTable = 'teams_dbtable';
	protected $tournamentsTable = 'tournaments';
	protected $tournamentPlayersTable = 'tournament_players';
	protected $tournamentTeamsTable = 'tournament_teams';
	protected $userHistoryTable = 'user_history_dbtable';
	protected $userTable = 'users_dbtable';
	protected $venuesTable = 'venues_dbtable';
	
	//misc other
	protected $jQueryPage = '/GlobalFiles/jquery2.0.2.js';
	protected $styleRoot = 'http://data.perpetualmotion.org/control/Global/Style/';

    public function __construct($db, $logger) {
        $this->db = $db;
		$this->logger = $logger;
    }

}
