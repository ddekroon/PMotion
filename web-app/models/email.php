<?php

class Models_Email extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
	
	protected $emailType = null;
	
    protected $subject = null;
    protected $content = null;
	
	protected $toEmail = null;
	protected $fromName = '';
	protected $fromEmail = null;
	protected $ccEmail = null;
	protected $bccEmail = null;
	
	protected $createdDate = null;
	protected $sentDate = null;
	
	protected $errorMsg = null;
	
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::emailsTable . " WHERE id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
		if(empty($data)) {
			return;
		}
		
		if(isset($data['id'])) {
            $this->id = $data['id'];
        }
		
		$this->emailType = $data['email_type'];
		
		$this->subject = $data['subject'];
		$this->content = $data['content'];

		$this->toEmail = $data['toEmail'];
		$this->fromName = $data['fromName'];
		$this->fromEmail = $data['fromEmail'];
		$this->ccEmail = $data['ccEmail'];
		$this->bccEmail = $data['bccEmail'];

		$this->createdDate = $data['createdDate'] != null ? new DateTime($data['createdDate']) : null;
		$this->sentDate = $data['sentDate'] != null ? new DateTime($data['sentDate']) : null;
		
		$this->errorMsg = $data['error_msg'];
	}

    function getId() {
		return $this->id;
	}

	function getEmailType() {
		return $this->emailType;
	}
	
	function getSubject() {
		return $this->subject;
	}

	function getContent() {
		return $this->content;
	}

	function getToEmail() {
		return $this->toEmail;
	}

	function getFromName() {
		return $this->fromName;
	}
	
	function getFromEmail() {
		return $this->fromEmail;
	}

	function getCcEmail() {
		return $this->ccEmail;
	}

	function getBccEmail() {
		return $this->bccEmail;
	}

	function getCreatedDate() {
		return $this->createdDate;
	}

	function getSentDate() {
		return $this->sentDate;
	}
	
	function setEmailType($emailType) {
		$this->emailType = $emailType;
	}

	function setSubject($subject) {
		$this->subject = $subject;
	}

	function setContent($content) {
		$this->content = $content;
	}

	function setToEmail($toEmail) {
		$this->toEmail = $toEmail;
	}

	function setFromName($fromName) {
		$this->fromName = $fromName;
	}
	
	function setFromEmail($fromEmail) {
		$this->fromEmail = $fromEmail;
	}

	function setCcEmail($ccEmail) {
		$this->ccEmail = $ccEmail;
	}

	function setBccEmail($bccEmail) {
		$this->bccEmail = $bccEmail;
	}

	function setCreatedDate($createdDate) {
		$this->createdDate = $createdDate;
	}

	function setSentDate($sentDate) {
		$this->sentDate = $sentDate;
	}
	
	function getErrorMsg() {
		return $this->errorMsg;
	}

	function setErrorMsg($errorMsg) {
		$this->errorMsg = $errorMsg;
	}

	public function save() {
		
		if($this->getSubject() == null || empty($this->getSubject()) 
				|| $this->getEmailType() == null || empty($this->getEmailType()) 
				|| $this->getContent() == null || empty($this->getContent())
				|| $this->getFromEmail() == null || empty($this->getFromEmail())
		) {
			$this->logger->error("Trying to save email with content missing.\n"
					. "Email Type: " . $this->getEmailType() . "\n"
					. "Subject: " . $this->getSubject() . "\n"
					. "Content: " . $this->getContent() . "\n"
					. "FromEmail: " . $this->getFromEmail() . "\n"); 
			return;
		}
		
		try {
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::emailsTable . " "
					. "(
						email_type, subject, content, to_email, from_name, from_email, cc_email, bcc_email, created_date
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, NOW())"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getEmailType(), 
					$this->getSubject(), 
					$this->getContent(), 
					$this->getToEmail(), 
					$this->getFromName(),
					$this->getFromEmail(), 
					$this->getCcEmail(), 
					$this->getBccEmail()
				)
			); 
			$this->setId($this->db->lastInsertId());
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}
	
	public function update() {
		try {
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::emailsTable . " SET "
					. "
						email_type = ?, 
						subject = ?, 
						content = ?, 
						to_email = ?, 
						from_name = ?, 
						from_email = ?, 
						cc_email = ?, 
						bcc_email = ?, 
						sent_date = ?,
						error_msg = ?
					WHERE id = ?"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getEmailType(), 
					$this->getSubject(), 
					$this->getContent(), 
					$this->getToEmail(), 
					$this->getFromName(), 
					$this->getFromEmail(), 
					$this->getCcEmail(), 
					$this->getBccEmail(),
					$this->getSentDate() != null ? $this->getSentDate()->format('Y-m-d H:i:s') : null,
					$this->getErrorMsg(),
					$this->getId()
				)
			); 
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}
}
