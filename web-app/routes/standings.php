<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$app->get('/standings/{leagueID}', function(Request $request, Response $response) {

		$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		
		if($league != null) {
			$leaguesController->setLeagueWeekInStandings($league);
		}

		return $this->view->render($response, "standings.phtml", [
				"request" => $request,
				"league" => $league,
				"teamController" => new Controllers_TeamsController($this->db, $this->logger),
				"leagueController" => $leaguesController,
				"isDashboard" => false,
				"router" => $this->router
			]
		);
	})->setName('standings')->add($defaultTemplate);
	
	$app->get('/schedule/{leagueID}', function(Request $request, Response $response) {

		$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		
		if($league != null) {
			$leaguesController->setLeagueWeek($league);
		}

		return $this->view->render($response, "standings.phtml", [
				"request", $request,
				"league" => $league
			]
		);
	})->setName('schedule')->add($defaultTemplate);
	
	//List Team Pages for League
	$app->get('/league/{leagueID}/team-pages', function (Request $request, Response $response) {

		$league = Models_League::withID($this->db, $this->logger, $request->getAttribute('leagueID'));

		return $this->view->render($response, "league-team-pages.phtml", [
				"request" => $request,
				"league" => $league,
				"router" => $this->router,
				"isDashboard" => false
		]); 
	})->setName('league-team-pages');

	//Team Page
	$app->get('/team/{teamID}', function (Request $request, Response $response) {

		$team = Models_Team::withID($this->db, $this->logger, $request->getAttribute('teamID'));

		if($team->getLeague() != null) {
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			$leaguesController->setLeagueWeekInStandings($team->getLeague());
		}
		
		return $this->view->render($response, "team-page.phtml", [
				"request" => $request,
				"team" => $team,
				"router" => $this->router,
				"isDashboard" => false,
				"leagueController" => new Controllers_LeaguesController($this->db, $this->logger),
				"teamController" => new Controllers_TeamsController($this->db, $this->logger)
		]); 
	})->setName('team-page');
?>