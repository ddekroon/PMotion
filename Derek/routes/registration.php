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
			
			$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
			$pastTeam = Models_Team::withID($this->db, $this->logger, $request->getAttribute('pastTeamID'));

			return $this->view->render($response, "registration/home.phtml", [
					"request", $request,
					"router" => $this->router,
					"isDashboard" => true,
					"sport" => $sport,
					"pastTeam" => $pastTeam,
					"user" => $curUser
				]
			);

			//return $response;
		})->setName('dashboard-register-team');
		
	})->add($dashboard)->add($authenticate);
	
	

?>