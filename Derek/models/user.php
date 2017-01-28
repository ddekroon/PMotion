<?php

class Models_User {
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

	/**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
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
	
	function toString() {
		return "User Object [ " . $this->getId() . " ]\n";
	}
}
