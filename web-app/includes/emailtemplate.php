<?php

class Includes_EmailTemplate {
	
	protected $emailType;
	protected $toAddresses = [];
	protected $fromName;
	protected $fromAddress;
	protected $subject;
	protected $templateLink;
	
	public function __construct($emailType, $toAddresses, $subject, $templateLink, $fromName, $fromAddress) {
		$this->emailType = $emailType;
		$this->toAddresses = $toAddresses;
		$this->subject = $subject;
		$this->templateLink = $templateLink;
		$this->fromName = $fromName;
		$this->fromAddress = $fromAddress;
	}
	
	public static function createEmailTemplate($emailType, $toAddresses, $subject, $templateLink, $fromName, $fromAddress) {
		return new Includes_EmailTemplate($emailType, $toAddresses, $subject, $templateLink, $fromName, $fromAddress);
	}
	
	function getEmailType() {
		return $this->emailType;
	}
	
	function getToAddresses() {
		return $this->toAddresses;
	}
	
	function getFromName() {
		return $this->fromName;
	}
	
	function getFromAddress() {
		return $this->fromAddress;
	}

	function getSubject() {
		return $this->subject;
	}

	function getTemplateLink() {
		return $this->templateLink;
	}
}

