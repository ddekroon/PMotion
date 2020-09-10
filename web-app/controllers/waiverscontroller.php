<?php

class Controllers_WaiversController extends Controllers_Controller {
		
	public function submitWaiver($request) {
		
		$allPostVars = $request->getParsedBody();
				
		$waiver = new Models_Waiver();
		$waiver->setDb($this->db);
		$waiver->setLogger($this->logger);
		$waiver->setName($allPostVars['name']);
		$waiver->setEmail($allPostVars['email']);
		$waiver->setGuardName($allPostVars['guardName']);
		$waiver->setGuardEmail($allPostVars['guardEmail']);
		$waiver->setSportId($allPostVars['sportID']);
		
		$this->validateWaiver($waiver);

		$waiver->saveOrUpdate();
	}

	function validateWaiver($waiver) {
		$errorString = "";
		
		if($waiver->getName() == "") {
			$errorString .= 'Please enter your name\n';
		} else if (!Includes_Helper::isValidName($waiver->getName())) {
			$errorString .= 'Please enter a valid name composed of: letters, numbers, and the caharacters \' and -\n';
		}

		if($waiver->getEmail() == "") {
			$errorString .= 'Please enter your email\n';
		} else if (!Includes_Helper::isValidEmail($waiver->getEmail())) {
			$errorString .= 'Please enter a valid email\n';
		}

		if($waiver->getGuardName() != "" && !Includes_Helper::isValidName($waiver->getGuardName())) {
			$errorString .= 'Please enter a valid guardian name composed of: letters, numbers, and the caharacters \' and -\n';
		}

		if($waiver->getGuardEmail() != "" && !Includes_Helper::isValidEmail($waiver->getGuardEmail())) {
  			$errorString .= 'Please enter a valid guardian\'s email\n';
		}
		if($waiver->getGuardEmail() != "" && $waiver->getGuardName() == "") {
			$errorString .= 'Please enter your guardian\'s name\n';
		}
		if($waiver->getGuardName() != "" && $waiver->getGuardEmail() == "") {
			$errorString .= 'Please enter your guardian\'s email\n';
		}
		if($waiver->getEmail() == $waiver->getGuardEmail() && $waiver->getEmail() != "") {
			$errorString .= 'Please enter two separate email\'s\n';
		}

		if(strlen($errorString) > 0) {
			throw new Exception($errorString);
		}
	}
}
