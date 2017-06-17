<?php

interface Models_Interface {
	
	/**
	 * Will load the model via the id associated with the row of the table.
	 * 
	 * @param type $db - db connection from the phpSlim container.
	 * @param type $id - the id of the row you want to load.
	 */
	public static function withID($db, $logger, $id);

	/**
	 * Creating an object from a row of data from the db. I suppose the row could also be an array you populate in php but that seems like bad style.
	 * @param array $row - your row of data from the db.
	 */
    public static function withRow($db, $logger, array $row);

	/**
	 * Should be a protected method but I guess you can't protect methods in an interface. Loads the content from the db
	 * @param type $db
	 * @param type $id
	 */
    function loadByID($db, $logger, $id);
	
	/**
	 * Should be a protected method but I guess you can't protect methods in an interface. Fills data in the object with data from the array.
	 * @param array $data
	 */
	function fill(array $data);
	
	public function saveOrUpdate();
	
	public function save();
	
	public function update();
}
