<?php

class Models_Generic implements JsonSerializable {
    protected $id;
	
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
    }

    public function getId() {
        return $this->id;
    }
	
	public function toString() {
		return "Generic Model [$this->id]";
	}
	
	public function jsonSerialize() {
		return "{ id: $this->id }";
	}
}
