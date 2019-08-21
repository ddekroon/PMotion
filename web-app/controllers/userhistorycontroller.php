<?php

class Controllers_UserHistoryController extends Controllers_Controller {
		
	function logUserHistory($user, $message, $description) {
		
		if(!isset($user) || $user->getId() == null || !isset($message) || empty($message)) {
			return;
		}
		
		try {
		
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::userHistoryTable . " "
				. "(
						user_history_user_id, 
						user_history_username, 
						user_history_type,
						user_history_description,
						user_history_timestamp
				) "
				. "VALUES " 
				. "(?, ?, ?, ?, NOW())"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$user->getId(),
					$user->getUsername(),
					$message,
					isset($description) ? $description : ''
				)
			); 
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}
}
