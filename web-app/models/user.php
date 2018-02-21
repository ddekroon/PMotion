<?php

class Models_User extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $username = '';
    protected $hashedPassword = '';
	protected $firstName = '';
	protected $lastName = '';
	protected $email = '';
	protected $phone = 0;
	protected $gender = '';
	protected $verifyCode = '';
	protected $access = 0;
	protected $createdDate;
	
	protected $regPassword = '';
	protected $regPasswordConfirm = '';
	
	protected $teams;
	
	
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::userTable . " WHERE user_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
		// no id if we're creating
        if(isset($data['user_id'])) {
            $this->id = $data['user_id'];
        }
		
        $this->username = $data['user_username'];
        $this->hashedPassword = $data['user_password'];
		$this->firstName = $data['user_firstname'];
		$this->lastName = $data['user_lastname'];
		$this->email = $data['user_email'];
		$this->phone = $data['user_phone'];
		$this->gender = $data['user_sex'];
		$this->verifyCode = $data['user_verify_code'];
		$this->access = $data['user_all_access'];
		$this->createdDate =  new DateTime($data['user_created']);
	}
	
	function getTeams() {
				
		if($this->teams == null && $this->db != null && $this->getId() != null) {
						
			$this->teams = [];
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_managed_by_user_id = " . $this->getId() . " AND team_deleted = 0";
			
			$stmt = $this->db->query($sql);

			while(($row = $stmt->fetch()) != false) {
				$this->teams[] = Models_Team::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->teams;
	}

    function getId() {
		return $this->id;
	}

	function getUsername() {
		return $this->username;
	}

	function getHashedPassword() {
		return $this->hashedPassword;
	}

	function getFirstName() {
		return $this->firstName;
	}

	function getLastName() {
		return $this->lastName;
	}

	function getEmail() {
		return $this->email;
	}

	function getPhone() {
		return $this->phone;
	}

	function getGender() {
		return $this->gender;
	}

	function getVerifyCode() {
		return $this->verifyCode;
	}

	function getAccess() {
		return $this->access;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setUsername($username) {
		$this->username = $username;
	}

	function setHashedPassword($hashedPassword) {
		$this->hashedPassword = $hashedPassword;
	}

	function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	function setEmail($email) {
		$this->email = $email;
	}

	function setPhone($phone) {
		$this->phone = $phone;
	}

	function setGender($gender) {
		$this->gender = $gender;
	}

	function setVerifyCode($verifyCode) {
		$this->verifyCode = $verifyCode;
	}

	function setAccess($access) {
		$this->access = $access;
	}
	
	function getCreatedDate() {
		return $this->createdDate;
	}

	function getRegPassword() {
		return $this->regPassword;
	}

	function getRegPasswordConfirm() {
		return $this->regPasswordConfirm;
	}

	function setCreatedDate($createdDate) {
		$this->createdDate = $createdDate;
	}

	function setRegPassword($regPassword) {
		$this->regPassword = $regPassword;
	}

	function setRegPasswordConfirm($regPasswordConfirm) {
		$this->regPasswordConfirm = $regPasswordConfirm;
	}
	
	function validateRegistration() {

		$error = '';
		$userController = new Controllers_UsersController($this->db, $this->logger);
		
		//This makes sure they did not leave any fields blank
		if ($this->getFirstName() == null
				|| $this->getLastName() == null
				|| $this->getEmail() == null
				|| $this->getPhone() == null
				|| $this->getGender() == null
				|| $this->getUsername() == null
				|| $this->getRegPassword() == null
				|| $this->getRegPasswordConfirm() == null) {
			$error .= "Some of the required fields are missing from this registration.\n"; //Shouldn't be possible with frontend javascript validation
		}

		// checks if the username is in use
		if($userController->getUserByUsername($this->getUsername()) != null) {
			$error .= "The username " . $this->getUsername() . " is already in use.\n";
		}

		//checks that the user name is between 6 and 16 characters
		$userLength = strlen($this->getUsername());
		if($userLength <= 5 || $userLength >= 17) {
			$error .= "Username must be between 6 and 16 characters.\n";
		}

		//checks that the password is between 6 and 16 characters
		$passLength = strlen($this->getRegPassword());
		if($passLength <= 5 || $passLength >= 17) {
			$error .= "Password must be between 6 and 16 characters.\n";
		}

		// checks if the email is in use
		if($userController->getUserByEmail($this->getEmail()) != null) {
			$error .= 'There is already an account registered with the email ' . $this->getEmail() . "\n";
		}

		//check if the email is invalid and give an error
		if (!Includes_Helper::isValidEmail($this->getEmail())) {
			$error .= "The email address " . $this->getEmail() . " is not valid (ex. 'something@something.com').\n";
		}
		
		//check if the phone number is proper (10 digits, formatting doesn't matter), throw error if not
		if(!preg_match('^(\D*)?(\d{3})(\D*)?(\d{3})(\D*)?(\d{4})$^', $this->getPhone())){
			$error.="You entered an invalid phone number (Please provide 10 digits in the format (xxx) xxx-xxxx)\n";
		}

		if (!Includes_Helper::isValidUsername($this->getUsername())){
			$error .= "The username entered contained invalid characters. No spaces, apostrophes, or quotes please.\n";
		}

		// this makes sure both passwords entered match
		if ($this->getRegPassword() != $this->getRegPasswordConfirm()) {
			$error .= "Your passwords don't match\n";
		}

		//If there are errors, exit the program and show the messages
		if(!empty($error)) {
			return "The following errors were found in your submission. Please correct and resubmit.\n\n$error";
		}
	}
	
	function save() {
		
		try {			
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::userTable . " "
					. "(
						user_username, user_firstname, user_lastname, user_password, user_email, user_phone, user_sex, 
						user_verify_code, user_all_access, user_created
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getUsername(), 
					$this->getFirstName(), 
					$this->getLastName(), 
					$this->getHashedPassword(), 
					$this->getEmail(), 
					$this->getPhone(), 
					$this->getGender(),
					$this->getVerifyCode()
				)
			); 
			$this->setId($this->db->lastInsertId());
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
		
		$userHistoryController = new Controllers_UserHistoryController($this->db, $this->logger);
		$userHistoryController->logUserHistory($this, 'Registered successfully as a new user.', '');
	}
	
	function update() {
		try {			
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::userTable . " SET "
					. "
						user_firstname = ?, 
						user_lastname = ?, 
						user_password = ?, 
						user_email = ?, 
						user_phone = ?, 
						user_sex = ?,
						user_verify_code = ?
					WHERE user_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getFirstName(), 
					$this->getLastName(), 
					$this->getHashedPassword(), 
					$this->getEmail(), 
					$this->getPhone(), 
					$this->getGender(),
					$this->getVerifyCode(),
					$this->getId()
				)
			); 
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}
}
