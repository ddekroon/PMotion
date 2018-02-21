<?php

	class Controllers_EmailsController extends Controllers_Controller {

		private $templateEngine;
		private $emailerEngine;
		
		private $host = 'ssl://smtp.gmail.com:465';
		
		public function __construct($db, $logger) {
			parent::__construct($db, $logger);
			$this->emailerEngine = new Includes_PHPMailer();
			$this->emailerEngine->IsSMTP();
			$this->emailerEngine->set('Host', $this->host);
			$this->emailerEngine->set('SMTPAuth', TRUE);
			$this->emailerEngine->isHTML();
		}
				
		public function createAndSendEmail($emailType, $subject, $content, $toEmail, $fromName, $fromEmail, $ccEmail, $bccEmail) {
			$email = $this->createEmail($emailType, $subject, $content, $toEmail, $fromName, $fromEmail, $ccEmail, $bccEmail);
			
			if(empty($email)) {
				throw new Exception("Couldn't create email.");
			}
			
			$this->sendEmail($email);
		}
		
		public function createEmail($emailType, $subject, $content, $toEmail, $fromName, $fromEmail, $ccEmail, $bccEmail) {
			$email = Models_Email::withRow($this->db, $this->logger, []);
			
			$email->setEmailType($emailType);
			$email->setSubject($subject);
			$email->setContent($content);
			$email->setToEmail($toEmail);
			$email->setFromName(!empty($fromName) ? $fromName : '');
			$email->setFromEmail($fromEmail);
			$email->setCcEmail($ccEmail);
			$email->setBccEmail($bccEmail);
			
			$email->save();
			
			return $email;
		}
			
		public function sendEmail($email) {
			
			$this->emailerEngine->ClearAddresses(); //This is very important in case there's some caching anywhere in the system.
			
			$propsController = new Controllers_PropertiesController($this->db, $this->logger);
					
			$this->emailerEngine->set('From', $email->getFromEmail());
			$this->emailerEngine->set('Username', $email->getFromEmail());
			$this->emailerEngine->set('FromName', $email->getFromName());
			$this->emailerEngine->set('Password', $propsController->getPropertyValue($email->getFromEmail() . '_password'));
			
			$this->emailerEngine->set('Body', $email->getContent());
			$this->emailerEngine->set('Subject', $email->getSubject());
			
			foreach(explode(",", $email->getToEmail()) as $curEmail) {
				$this->emailerEngine->AddAddress($curEmail);
			}
			
			foreach(explode(",", $email->getCcEmail()) as $curEmail) {
				$this->emailerEngine->AddCC($curEmail);
			}
			
			foreach(explode(",", $email->getBccEmail()) as $curEmail) {
				$this->emailerEngine->AddBCC($curEmail);
			}
			
			if($this->emailerEngine->Send()) {
				$email->setSentDate(new DateTime());
				$email->update();
			} else {
				$this->logger->error("Email not sent: " . $email . "\n" . $this->emailerEngine->ErrorInfo);
				$email->setErrorMsg($this->emailerEngine->ErrorInfo);
				$email->update();
			}
		}
	}

?>