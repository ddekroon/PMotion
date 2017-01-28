<?php

class Models_AuthToken {
    protected $id;
    protected $selector;
    protected $token;
	protected $userId;

	/**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
		
        $this->selector = $data['selector'];
        $this->token = $data['token'];
		$this->userId = $data['userId'];
    }

    function getId() {
		return $this->id;
	}

	function getSelector() {
		return $this->selector;
	}

	function getToken() {
		return $this->token;
	}

	function getUserId() {
		return $this->userId;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setSelector($selector) {
		$this->selector = $selector;
	}

	function setToken($token) {
		$this->token = $token;
	}

	function setUserId($userId) {
		$this->userId = $userId;
	}
}
