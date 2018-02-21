<?php

	class Controllers_PropertiesController extends Controllers_Controller {
		
		public function getProperty($key) {
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::propertiesTable . " WHERE `key` = ?";

			$stmt = $this->db->prepare($sql);
			if($stmt->execute(array($key))) {
				if(($row = $stmt->fetch()) != false) {
					return Models_Property::withRow($this->db, $this->logger, $row);
				}
			}
			
			return null;
		}
		
		public function getPropertyValue($key) {
			
			$prop = $this->getProperty($key);
			
			if($prop != null) {
				return $prop->getValue();
			}
			
			return null;
		}
		
		public function saveOrUpdateProperty($key, $value) {
			
			if(empty($key)) {
				return null;
			}
			
			$prop = $this->getProperty($key);
			
			if(empty($prop)) {
				$prop = Models_Property::withID($this->db, $this->logger, -1);
				$prop->setKey($key);
			}
			
			$prop->setValue($value);
			$prop->saveOrUpdate();
			
			return $prop;
		}
	}

?>