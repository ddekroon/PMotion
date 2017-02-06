<?php

class Models_Sport extends Models_Generic implements JsonSerializable {
    protected $name;
    protected $registrationDueDate;
	protected $defaultPicLink;
	protected $logoLink;
	
	/**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['sport_id'])) {
            $this->id = $data['sport_id'];
        }
		
        $this->name = $data['sport_name'];
        $this->registrationDueDate = $data['sport_registration_due_date'];
		$this->defaultPicLink = $data['sport_default_pic_link'];
		$this->logoLink = $data['sport_logo_link'];
    }

    public function getDayNumber() {
        return $this->dayNumber;
    }
	
	function getName() {
		return $this->name;
	}

	function getRegistrationDueDate() {
		return $this->registrationDueDate;
	}

	function getDefaultPicLink() {
		return $this->defaultPicLink;
	}

	function getLogoLink() {
		return $this->logoLink;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setRegistrationDueDate($registrationDueDate) {
		$this->registrationDueDate = $registrationDueDate;
	}

	function setDefaultPicLink($defaultPicLink) {
		$this->defaultPicLink = $defaultPicLink;
	}

	function setLogoLink($logoLink) {
		$this->logoLink = $logoLink;
	}
	
	function jsonSerialize() {
		return "{"
				. "id:" . $this->getId() . ","
				. "registrationDueDate:" . $this->getRegistrationDueDate() . ","
				. "defaultPicLink:" . $this->getDefaultPicLink() . ","
				. "logoLink:" . $this->getLogoLink() . ","
			. "}";
		
	}
}
