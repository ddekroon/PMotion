<?php

class Models_User extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $username;
    protected $hashedPassword;
	protected $firstName;
	protected $lastName;
	protected $email;
	protected $phone;
	protected $gender;
	protected $verifyCode;
	protected $access;
	protected $createdDate;
	
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
}
