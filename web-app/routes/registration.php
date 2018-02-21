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
		
		$app->get('/registration-success', function (Request $request, Response $response) {
			
			$curUser = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]);
			
			return $this->view->render($response, "registration/registration-success.phtml", [
					"request", $request,
					"router" => $this->router,
					"isDashboard" => true,
					"user" => $curUser
				]
			);
		})->setName('registration-success');
		
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

	$app->post('/remove-team/{teamID}', function(Request $request, Response $response) {

		$returnObj = array();
		$teamsController = new Controllers_TeamsController($this->db, $this->logger);
		
		try {
			
			$team = Models_Team::withID($this->db, $this->logger, (int)$request->getAttribute('teamID'));
			
			if($team != null && $team->getId() != null) {
				$teamsController->removeTeam($team);
				$returnObj["status"] = 1;
			} else {
				$returnObj["status"] = 0;
			}
		} catch(Exception $e) {
			$this->logger->debug("Caught Exception: " . $e . "\n");
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
	})->setName('remove-team')->add($authenticate);
	
	//Create Account
	$app->get('/create-account', function (Request $request, Response $response) {

		return $this->view->render($response, "registration/edit-profile.phtml", [
			"request" => $request,
			"router" => $this->router
		]);
	})->setName('create-account')->add($defaultTemplate);
	
	$app->post('/submit-account', function (Request $request, Response $response) {
		$returnObj = array();
		
		$usersController = new Controllers_UsersController($this->db, $this->logger);

		try {
			$usersController->saveProfile(null, $request);
			$returnObj["status"] = 1;

		} catch(Exception $e) {
			$this->logger->debug("Caught Exception: " . $e . "\n");
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
		
	})->setName('submit-account');
	
	
	//Forgot Password
	$app->get('/reset-password[/{validationKey}]', function (Request $request, Response $response) {
		
		$validationKey = $request->getAttribute('validationKey');
		
		if(isset($validationKey) && !empty($validationKey)) {
			$userController = new Controllers_UsersController($this->db, $this->logger);
			$user = $userController->getUserByValidationKey($validationKey);
		}
		
		return $this->view->render($response, "registration/reset-password.phtml", [
			"request" => $request,
			"router" => $this->router,
			"isKeyValid" => isset($user) && $user->getId() != null,
			"username" => isset($user) ? $user->getUsername() : "",
			"validationKey" => $validationKey
		]);
	})->setName('reset-password')->add($defaultTemplate);
	
	$app->post('/request-reset-password', function (Request $request, Response $response) {
		$returnObj = array();
		
		$usersController = new Controllers_UsersController($this->db, $this->logger);

		try {
			$usersController->startResetPasswordProcess($request);
			$returnObj["status"] = 1;
			$returnObj["successMessage"] = "Password reset successfully. Please check your email for a link to reset your new password.";
			
		} catch(Exception $e) {
			$this->logger->debug("Caught Exception: " . $e . "\n");
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
		
	})->setName('request-reset-password');
	
	$app->post('/submit-reset-password/{validationKey}', function (Request $request, Response $response) {
		$returnObj = array();
		
		$usersController = new Controllers_UsersController($this->db, $this->logger);
		$validationKey = $request->getAttribute('validationKey');
		
		try {
			$usersController->finishResetPasswordProcess($validationKey, $request);
			$returnObj["status"] = 1;
			$returnObj["successMessage"] = "Password reset successfully.";
			
		} catch(Exception $e) {
			$this->logger->debug("Caught Exception: " . $e . "\n");
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
		
	})->setName('submit-reset-password');
?>