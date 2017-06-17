<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once(realpath(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

//Load classes in the models folder.
spl_autoload_register(function($classname) {
	$filename = str_replace('_', DIRECTORY_SEPARATOR, strtolower($classname)).'.php';
	$file = $filename;

    if (!file_exists($file)) {
        return FALSE;
    }
    include_once($file);
	
    //require ("Controllers/" . $classname . ".php");
});

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

require_once('secrets.php');

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['view'] = new \Slim\Views\PhpRenderer("views/");

//Remove the trailing / on any URL if it exists.
$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        
        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

$authenticate = function ($request, $response, $next) {
		
	session_start();
	
	$login = new Controllers_AuthController($this->db, $this->logger);
	
	if($login->shouldAuthenticate($request)) { //authenticated
		$response = $next($request, $response);
	} else {
		session_unset();
		session_destroy();
		$response = $response->withRedirect($this->router->pathFor('login'), 303);
	}
		
	return $response;
};

$dashboard =  function ($request, $response, $next) {
	
    $response = $this->view->render($response, 'template/dashboard-header.phtml', [
		"user" => Models_User::withID($this->db, $this->logger, $_SESSION[Controllers_AuthController::SESSION_USER_ID]),
		"router" => $this->router
	]);
    $response = $next($request, $response);
    $response = $this->view->render($response, 'template/dashboard-footer.phtml');
	
	return $response;
};

include('routes/api/sports.php');
include('routes/default.php');
include('routes/score-reporter.php');
include('routes/dashboard.php');
include('routes/registration.php');

$app->run();

?>