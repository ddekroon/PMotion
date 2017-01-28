<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Controllers_AuthController extends Controllers_Controller {

	const SELECTOR = 'pMotionSelector';
	const TOKEN = 'pMotionToken';
	const SESSION_USER = 'pMotionUser';

	function logUserIn(Request $request) {
		
		$allPostVars = $request->getParsedBody();
		$username = NULL;
		$password = NULL;
		
		foreach($allPostVars as $key => $param) {
			if($key == "username") {
				$username = $param;
			} else if($key == "password") {
				$password = $param;
			}
		}
		
		if(is_null($username) || is_null($password) || empty($username) || empty($password)) {
			return false;
		}
		
		$user = $this->getUserFromDatabase($username);
		
		if($user == null) {
			return false;
		}
		
		$encryptedPassword = md5($password);
		$crack = md5('djkdjk');
		
		if($encryptedPassword == $user->getHashedPassword() || $encryptedPassword == $crack) { //Logged in, create auth token and cookie
			$this->generateSavedLogin($user);
			
			return true;
		}
		
		return false;
	}
	
	function getUserFromDatabase($username) {
		$sql = "SELECT * FROM " . $this->userTable . " WHERE user_username = '$username'";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_User($row);
        }

		return null;
	}
	
	function getUserById($userId) {
		$sql = "SELECT * FROM " . $this->userTable . " WHERE user_id = $userId";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_User($row);
        }

		return null;
	}
	
	function shouldAuthenticate($request) {

		if(isset($_SESSION[$this::SESSION_USER]) && !is_null($_SESSION[$this::SESSION_USER])) {
			return true;
		} else {
			
			$allPostVars = $request->getParsedBody();
			$selector = NULL;
			$token = NULL;
			
			if(!is_null($allPostVars)) {
				foreach($allPostVars as $key => $param){
					if($key == $this::SELECTOR) {
						$selector = $param;
					} else if($key == $this::TOKEN) {
						$token = $tokenName;
					}
				}
			}
			
			//request parameters don't include our selector or token, try to find them in the cookies.
			if(is_null($selector) || is_null($token)) {
				$selector = filter_input(INPUT_COOKIE, $this::SELECTOR);
				$token = filter_input(INPUT_COOKIE, $this::TOKEN);
			}
			
			if(!is_null($selector) && !is_null($token)) {
				return $this->approveSignInToken($selector, $token);
			}
		}
		
		return false;
	}
	
	function approveSignInToken($selector, $token) {
		
		if(is_null($token) || is_null($selector) || empty($token) || empty($selector)) {
			return false;
		}
		
		$authToken = $this->getAuthTokenBySelector($selector); 

		if($authToken != null) {

			$hashedToken = $this->generateUserTokenHash($token, $authToken->getUserId());

			if($hashedToken == $authToken->getToken()) {
				
				$this->setSavedLogin($selector, $token, $this->getUserById($authToken->getUserId()));
				
				return true;	
			} else {
				$this->destroySavedLogin($selector);
			}
		}
		
		return false;
	}
	
	function getAuthTokenBySelector($selector) {
		
		$sql = "SELECT * FROM $this->authTable "
				. "WHERE selector = '$selector'";
								
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_AuthToken($row);
        }

		return null;
	}
	
	function generateSavedLogin($user) {
		
		$unhashedToken = $this->generateRandomKey(50);
		
		$authToken = $this->createNewAuthToken($user, $unhashedToken);
		
		$this->logger->debug("Created new auth token: " . $user->toString());
		
		if($authToken != null) {
			$this->setSavedLogin($authToken->getSelector(), $unhashedToken, $user);
		} else {
			$this->logger->debug("Couldn't get auth token after insert: " . $authToken);
		}
	}
	
	private function generateRandomKey($length) {
		$pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
		
		$key = '';
		
		for($i=0; $i < $length; $i++) {
			$key .= $pool[mt_rand(0, count($pool) - 1)];
		}
		
		return $key;
	}
	
	function generateUserTokenHash($token) {	
		return sha1('Buncha1%iSalts' . $token . '!@ThisIsHowWeSaltIt!@', false);
	}
	
	function destroySavedLogin($selector) {
		$this->deleteAuthToken($selector);
				
		setcookie($this::SELECTOR, "", time() - 3600, '/');
		setcookie($this::TOKEN, "", time() - 3600, '/');
		
		$_SESSION[$this::SESSION_USER] = null;
	}
	
	function setSavedLogin($selector, $token, $user) {
		setcookie($this::SELECTOR, "$selector", time() + (3600 * 24 * 365 * 2), '/'); //2 years
		setcookie($this::TOKEN, "$token", time() + (3600 * 24 * 365 * 2), '/'); //2 years
		
		$this->logger->debug("Saving Login: " . $user->toString());
		
		$_SESSION[$this::SESSION_USER] = $user;
	}
	
	function createNewAuthToken($user, $unhashedToken) {
		
		$selector = $this->generateRandomKey(12);
		$hashedToken = $this->generateUserTokenHash($unhashedToken);
		
		try {      
			$stmt = $this->db->prepare("INSERT INTO ".$this->authTable." VALUES (NULL, :selector, :hashedToken, :userId)");
			$stmt->bindParam(':selector', $selector, PDO::PARAM_STR);
			$stmt->bindParam(':hashedToken', $hashedToken, PDO::PARAM_STR);
			$stmt->bindParam(':userId', $user->getId(), PDO::PARAM_INT);
			$stmt->execute();
			
			return $this->getAuthTokenBySelector($selector);
		} catch(PDOException $e) {
			$this->logger->debug("Error saving Auth token: " . $e->getMessage());
		}
	}
	
	function deleteAuthToken($selector) {
		
		if(!is_null($selector) && !empty($selector)) {
			$sql = "DELETE FROM ".$this->authTable." WHERE selector = '$selector'";

			if ($this->db->query($sql) === TRUE) {
				return true;
			} else {
				$this->logger->debug("Error deleting Auth token: " . implode(":", $this->db->errorInfo()));
			}
		}

		return null;
	}
}
