<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	//Home - redirect to login if not logged in, otherwise direct to dashboard
	$app->get('/home', function (Request $request, Response $response) {
		return $response->withRedirect($this->router->pathFor('dashboard'), 303);
	})->setName('home')->add($authenticate);
	
	$app->get('/forbidden', function (Request $request, Response $response) {
		return $this->view->render($response, "forbidden.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('forbidden');
	
	//Login
	$app->get('/login', function (Request $request, Response $response) {
		return $this->view->render($response, "login.phtml", [
				"request" => $request,
				"router" => $this->router
			]
		);
	})->setName('login');
	
	$app->post('/login', function (Request $request, Response $response) {
		
		$login = new Controllers_AuthController($this->db, $this->logger);
		
		$returnObj = array();
		
		if($login->logUserIn($request)) {
			$returnObj["status"] = 1;
		} else {
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = "Invalid Credentials";
		}

		$response->getBody()->write(json_encode($returnObj));
		
		return $response;
	});
	
	
	//Logout
	$app->get('/logout', function (Request $request, Response $response) {
		
		$login = new Controllers_AuthController($this->db, $this->logger);
		$selector = $request->getAttribute(Controllers_AuthController::SELECTOR, NULL);

		if(is_null($selector)) {
			$selector = filter_input(INPUT_COOKIE, Controllers_AuthController::SELECTOR);
		}
		
		$login->destroySavedLogin($selector);
		
		session_unset();
		session_destroy();
		
		return $response->withRedirect($this->router->pathFor('login'), 303);
	})->setName('logout')->add($authenticate);
	
	$app->post('/logout', function (Request $request, Response $response) {
		
		$login = new Controllers_AuthController($this->db, $this->logger);
		$selector = $request->getAttribute(Controllers_AuthController::SELECTOR, NULL);

		if(is_null($selector)) {
			$selector = filter_input(INPUT_COOKIE, Controllers_AuthController::SELECTOR);
		}

		$login->destroySavedLogin($selector);
		
		$returnObj = array();
		$returnObj["status"] = 1;

		$response->getBody()->write(json_encode($returnObj));
		
		session_unset();
		session_destroy();
		
		return $response;
	})->add($authenticate);
	
	//Create Account
	$app->get('/create-account', function (Request $request, Response $response) {
		return $this->view->render($response, "coming-soon.phtml", []);
	})->setName('create-account');
	
	//Forgot Password
	$app->get('/reset-password', function (Request $request, Response $response) {
		return $this->view->render($response, "coming-soon.phtml", []);
	})->setName('reset-password');
	
	//Dashboard
	$app->get('/dashboard', function (Request $request, Response $response) {
		$user = Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]); //Load user from db, that way we refresh all user info.
		
		return $this->view->render($response, "dashboard.phtml", [
			"user" => $user,
			"router" => $this->router
		]);
		
	})->setName('dashboard')->add($dashboard)->add($authenticate);
	
	//Generic get league teams
	$app->get('/get-league-teams/{leagueID}', function(Request $request, Response $response) {
		$leagueID = (int)$request->getAttribute('leagueID');

		if($leagueID > 0) {
			$teamsController = new Controllers_TeamsController($this->db, $this->logger);

			$teams = $teamsController->getTeams($leagueID);
			$dataObj = array();
			$dataObj["teams"] = array();

			foreach($teams as $team) {
				$dataObj["teams"][] = $team->jsonSerialize();
			}

			$returnObj = array(
				"status" => 1,
				"data" => $dataObj
			);

			$response->getBody()->write(json_encode($returnObj));
		} else {
			$response->getBody()->write("{status:0, errorMessage:'Error: Invalid League ID.'}");
		}

		return $response->withHeader(
			'Content-Type',
			'application/json'
		);
	});

?>