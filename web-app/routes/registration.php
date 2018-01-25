<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->group('/dashboard/registration', function () use ($app) {
		
		$app->get('/home', function (Request $request, Response $response) {
			
			$curUser = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			
			$regSeasonArray = $seasonsController->getSeasonsAvailableForRegistration();
			
			if(sizeof($regSeasonArray) == 0) {
				$regSeason = new Models_Season();
			} else {
				$regSeason = $regSeasonArray[0];
			}
			
			return $this->view->render($response, "registration/home.phtml", [
					"request", $request,
					"router" => $this->router,
					"isDashboard" => true,
					"user" => $curUser,
					"regSeason" => $regSeason,
					"sports" => $sportsController->getSports()
				]
			);
		})->setName('dashboard-registration');
		
		$app->get('/register-team[/{sportID}[/{pastTeamID}]]', function (Request $request, Response $response) {

			$curUser = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
			
			$sportID = $request->getAttribute('sportID');
			$sport = Models_Sport::withID($this->db, $this->logger, $sportID);
			$pastTeam = Models_Team::withID($this->db, $this->logger, $request->getAttribute('pastTeamID'));
			
			$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
			$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);
			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			
			return $this->view->render($response, "dashboard/edit-team.phtml", [
					"request", $request,
					"router" => $this->router,
					"sport" => $sport,
					"team" => $pastTeam,
					"user" => $curUser,
					"registerTeam" => true,
					"league" => $pastTeam->getId() != null ? $pastTeam->getLeague() : new Models_League(),
					"leaguesAvailableForRegistration" => $leaguesController->getLeaguesForRegistration($sportID),
					"seasonsAvailableForRegistration" => $seasonsController->getSeasonsAvailableForRegistration(),
					"sports" => $sportsController->getSports()
				]
			);

			//return $response;
		})->setName('dashboard-register-team');
		
	})->add($dashboard)->add($authenticate);
	
	$app->post('/save-team[/{teamID}]', function (Request $request, Response $response) {

		$curUser = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
		$team = Models_Team::withID($this->db, $this->logger, (int)$request->getAttribute('teamID'));

		$teamsController = new Controllers_TeamsController($this->db, $this->logger);

		$returnObj = array();
				
		try {
			$successMessage = $teamsController->insertOrUpdateTeam($team, $curUser, $request);
			$returnObj["status"] = 1;
			$returnObj["successMessage"] = $successMessage;
		} catch(Exception $e) {
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;

	})->setName('save-team')->add($authenticate);

?>