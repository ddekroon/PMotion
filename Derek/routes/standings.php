<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$app->get('/standings/{leagueID}', function(Request $request, Response $response) {

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
	});
	
?>