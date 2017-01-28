<?php

class Models_Team {
    protected $id;
    protected $name;
    protected $teamNumInLeague;
	protected $leagueId;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
				
        // no id if we're creating
        if(isset($data['team_id'])) {
            $this->id = $data['team_id'];
        }
		
        $this->name = $data['team_name'];
        $this->leagueId = $data['team_league_id'];
		$this->teamNumInLeague = $data['team_num_in_league'];
    }

    public function getId() {
        return $this->id;
    }
	
	public function getName() {
		return $this->name;
	}
	
	public function getShortName() {
		return substr($this->name, 0, 20);
	}

	public function getTeamNumInLeague() {
		return $this->teamNumInLeague;
	}

	public function getLeagueId() {
		return $this->leagueId;
	}
	
	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
