<?php

class Models_Match {
    protected $id;
    protected $dateId;
	protected $oppTeamId;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
				
        // no id if we're creating
        if(isset($data['scheduled_match_id'])) {
            $this->id = $data['scheduled_match_id'];
        }
		
        $this->dateId = $data['scheduled_match_date_id'];
    }

    public function getId() {
        return $this->id;
    }
	
	public function getDateId() {
		return $this->dateId;
	}

	public function getOppTeamId() {
		return $this->oppTeamId;
	}
	
	public function setOppTeamId($oppTeamId) {
		$this->oppTeamId = $oppTeamId;
	}
}
