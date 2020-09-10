<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	
	$app->post('/api/waiver', function (Request $request, Response $response) {

		$waiversController = new Controllers_WaiversController($this->db, $this->logger);
		
		try {
			$waiversController->submitWaiver($request);

			$response = $response->withStatus(200);
			$response->getBody()->write("Waiver Submitted");
		} catch(Exception $ex) {
			$response = $response->withStatus(400);
			$response->getBody()->write("Couldn't submit waiver:\n" . $ex->getMessage());
		}
	});

?>