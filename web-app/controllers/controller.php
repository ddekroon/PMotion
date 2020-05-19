<?php

abstract class Controllers_Controller {
    protected $db;
	protected $logger;
	
	//misc other
	protected $jQueryPage = '/GlobalFiles/jquery2.0.2.js';
	protected $styleRoot = 'http://data.perpetualmotion.org/control/Global/Style/';

    public function __construct($db, $logger) {
        $this->db = $db;
		$this->logger = $logger;
    }

}
