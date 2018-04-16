<?php

class Controllers_UsersController extends Controllers_Controller {
		
	function getActiveScheduledMatchesForUser($user) {
		
		if(!isset($user) || $user == null || $user->getId() == null) {
			return [];
		}
			
		$sql = "SELECT sm.* FROM " . Includes_DBTableNames::scheduledMatchesTable . " as sm "
				. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON sm.scheduled_match_league_id = league.league_id "
				. "INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON league.league_season_id = season.season_id "
						. "AND (season.season_available_registration = 1 OR season.season_available_score_reporter = 1) "
				. "INNER JOIN " . Includes_DBTableNames::teamsTable . " teamOne ON teamOne.team_id = sm.scheduled_match_team_id_1 "
				. "INNER JOIN " . Includes_DBTableNames::teamsTable . " teamTwo ON teamTwo.team_id = sm.scheduled_match_team_id_2 "
				. "WHERE (teamOne.team_managed_by_user_id = " . $user->getId() . " AND teamOne.team_finalized = 1 AND teamOne.team_dropped_out = 0) "
				. "OR (teamTwo.team_managed_by_user_id = " . $user->getId() . " AND teamTwo.team_finalized = 1 AND teamTwo.team_dropped_out = 0)";

		$stmt = $this->db->query($sql);

		$results = [];

		while($row = $stmt->fetch()) {
			$results[] = Models_Team::withRow($this->db, $this->logger, $row);
		}

		return $results;
	}
	
	function getUserByUsername($username) {
		if(!isset($username) || empty($username)) {
			return null;
		}
		$sql = "SELECT * FROM " . Includes_DBTableNames::userTable . " WHERE user_username = :username";
		
		try {
		
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute(array(':username' => $username));
			
			if(($row = $stmt->fetch()) != false) {
				return Models_User::withRow($this->db, $this->logger, $row);
			}
		} catch(PDOException $e) {
			// error handling
		}
		
		return null;
	}
	
	function getUserByEmail($email) {
		if(!isset($email) || empty($email)) {
			return null;
		}
		$sql = "SELECT * FROM " . Includes_DBTableNames::userTable . " WHERE user_email = :email";
		
		try {
		
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute(array(':email' => $email));
			
			if(($row = $stmt->fetch()) != false) {
				return Models_User::withRow($this->db, $this->logger, $row);
			}
		} catch(PDOException $e) {
			// error handling
		}
		
		return null;
	}
	
	function getUserByValidationKey($key) {
		if(!isset($key) || empty($key)) {
			return null;
		}
		$sql = "SELECT * FROM " . Includes_DBTableNames::userTable . " WHERE user_verify_code = :key";
		
		try {
		
			$this->db->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			$stmt = $this->db->prepare($sql);

			$stmt->execute(array(':key' => $key));
			
			if(($row = $stmt->fetch()) != false) {
				return Models_User::withRow($this->db, $this->logger, $row);
			}
		} catch(PDOException $e) {
			// error handling
		}
		
		return null;
	}
	
	function saveProfile($user, $request) {
		
		if($user == null) {
			$user = Models_User::withID($this->db, $this->logger, -1);
		}
		
		$allPostVars = $request->getParsedBody();
		
		$user->setFirstName($allPostVars['userFirstName']);
		$user->setLastName($allPostVars['userLastName']);
		$user->setEmail($allPostVars['userEmail']);
		$user->setPhone(preg_replace("/\D/", '', $allPostVars['userPhoneNumber']));
		$user->setGender($allPostVars['userGender']);
		
		if($user->getId() == null) { //Registration
			$user->setUsername($allPostVars['username']);
			$user->setRegPassword($allPostVars['password']);
			$user->setRegPasswordConfirm($allPostVars['userConfirmPassword']);
			
			$error = $user->validateRegistration();
			
			if(!empty($error)) {
				throw new Exception($error);
			}
			
			$user->setHashedPassword(md5($allPostVars['password']));
			
			$user->save();
			
			$this->sendRegistrationEmail($user);
		} else {
			$user->update();
		}
	}

	function sendRegistrationEmail($user) {
		
		if(!isset($user) || empty($user) || $user->getId() == null) {
			return;
		}
		
		$emailBody="
			<table align='center'>
				<tr>
					<td colspan='2'>
						<b>THANK YOU FOR REGISTERING!</b>
					</td>
				</tr>
				<tr>
					<td colspan='2'>This email will serve as a password & username reminder.<br /><br /></td>
				</tr>
				<tr>
					<td>Username: </td>
					<td>" . $user->getUsername() . "</td>
				</tr>
				<tr>
					<td>Password: </td>
					<td>" . $user->getRegPassword() . "</td>
				</tr>
				<tr>
					<td colspan='2' width='500'>
						<br />Please keep this information in a safe place. Should you forget your username or password, 
						both are changeable at any time from our <a href='http://www.perpetualmotion.org/'>main website</a>.
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<br /><br />Thanks, enjoy the season!<br />
						<b>The Perpetual Motion Team</b><br />
						<a href='mailto:info@perpetualmotion.org'>info@perpetualmotion.org</a>
					</td>
				</tr>
			</table>";

		$subject= "New User Registered - " . $user->getUsername();
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$emailType = Includes_EmailTypes::userRegistered();
		
		$emailController->createAndSendEmail($emailType->getEmailType(), 
				$subject, 
				$emailBody, 
				$user->getEmail(), 
				$emailType->getFromName(), 
				$emailType->getFromAddress(), 
				null, 
				null
		);
	}
	
	function startResetPasswordProcess($request) {
		
		$allPostVars = $request->getParsedBody();
		
		$email = $allPostVars["userEmail"];
		
		if(isset($email) && !empty($email)) {
			$user = $this->getUserByEmail($email);
			
			if(isset($user) && $user->getId() != null) {
				$user->setHashedPassword(md5("djkdjkdjk"));
				
				$verificationCode = bin2hex(openssl_random_pseudo_bytes(16));
				$user->setVerifyCode($verificationCode);
				$user->update();
				
				$this->sendResetPasswordRequestEmail($user);
			} else {
				throw new Exception("Invalid email address, no account with that email exists.");
			}
		} else {
			throw new Exception("Please submit a valid email address.");
		}
	}
	
	function sendResetPasswordRequestEmail($user) {
		
		$resetPasswordLink = Includes_Links::RESET_PASSWORD . "/" . $user->getVerifyCode();
		
		$emailBody = "<html><body><p>Hi " . $user->getFirstName() . ",</p>"
			. "<p>"
				. "You're getting this email because a request for a changed password on an account with this email address has been submitted. "
				. "To reset your user information, please click the link below and fill in the required information."
			. "</p>"
			. "<p>"
				. "Username: <b>" . $user->getUsername() . "</b><br />"
				. "Validation Code: <b>" . $user->getVerifyCode() . "</b>"
			. "</p>"
			. "<p><a href='" . $resetPasswordLink . "'>" . $resetPasswordLink . "</a></p>"
		. "<p>Thanks,<br />"
		. "The Perpetual Motion Team.</body></html>";
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$emailType = Includes_EmailTypes::passwordResetRequest();
		
		$emailController->createAndSendEmail($emailType->getEmailType(), 
				$emailType->getSubject(), 
				$emailBody, 
				$user->getEmail(), 
				$emailType->getFromName(), 
				$emailType->getFromAddress(), 
				null, 
				null
		);
	}
	
	function finishResetPasswordProcess($validationKey, $request) {
		$allPostVars = $request->getParsedBody();
		
		if(isset($validationKey) && !empty($validationKey)) {
		
			$user = $this->getUserByValidationKey($validationKey);
			
			if(isset($user) && $user->getId() != null) {
				
				$pass = $allPostVars["password"];
				$confirmPass = $allPostVars['userConfirmPassword'];
				
				//Don't use default user validation because legacy user accounts might have now invalid characters and they can't change them.
				if($pass != $confirmPass) {
					throw new Exception("Password and Confirmation don't match.");
				}
				
				$user->setRegPassword($pass);
				$user->setHashedPassword(md5($pass));
				$user->setVerifyCode("");
				$user->update();
				
				$this->sendResetPasswordSuccessEmail($user);
			} else {
				throw new Exception("Invalid validation key, no account with that key exists. Check your email or try resetting your password again.");
			}
		} else {
			throw new Exception("Invalid request, check your email link and try again.");
		}
	}
	
	function sendResetPasswordSuccessEmail($user) {
		$emailBody = "<html><body><p>Hi " . $user->getFirstName() . ",</p>"
			. "<p>** Password Reset **</p>"
			. "<p>Below is your new login information:</p>"
			. "<p>"
				. "Username: <b>" . $user->getUsername() . "</b><br />"
				. "Password: <b>" . $user->getRegPassword() . "</b>"
			. "</p>"
		. "<p>Thanks,<br />"
		. "The Perpetual Motion Team.</body></html>";
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$emailType = Includes_EmailTypes::passwordResetSuccess();
		
		$emailController->createAndSendEmail($emailType->getEmailType(), 
				$emailType->getSubject(), 
				$emailBody, 
				$user->getEmail(), 
				$emailType->getFromName(), 
				$emailType->getFromAddress(), 
				null, 
				null
		);
	}
}
