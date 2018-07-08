<?php

class Controllers_RegistrationController extends Controllers_Controller {
	
	private $templateEngine;
	
	public function __construct($db, $logger) {
		parent::__construct($db, $logger);
		$this->templateEngine = new League\Plates\Engine(TEMPLATES_PATH);
	}
	
	function sendRegistrationEmail(Models_Team $team) {
		
		$teamsController = new Controllers_TeamsController($this->db, $this->logger);
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
	
		$returningString = $teamsController->getIsReturningTeam($team) ? 'Returning' : 'New';
		$isComment = $team->getRegistrationComment() != null && !empty($team->getRegistrationComment());
		$captain = $team->getCaptain();
		$howHeardMethod = $captain->getHowHeardMethod() > 0 ? Includes_HeardAboutUsMethods::getMethodByOrdinal($captain->getHowHeardMethod()) : null;
		$howHeardOther = $captain->getHowHeardMethod() > 0 ? $captain->getHowHeardOtherText() : '';

		$subject= 'Registration Confirmation - ' . $team->getName() . ' - ' . $team->getLeague()->getFormattedName() 
				. ' - ' . $team->getLeague()->getSeason()->getName();
		
		$adminSubject = 'Reg - ' . ($isComment ? 'Com - ' : '') . $team->getName() . ' - ' . $team->getLeague()->getFormattedName() 
				. ' - ' . $team->getLeague()->getSeason()->getName();
		
		if($team->getLeague()->getIsFullTeams()) {
			$subject .= ' - Full - Waiting List';
			$adminSubject .= ' - Full - Waiting List';
		}

		$regEmailTemplate = Includes_EmailTypes::teamRegistered();
		
		$params = [
			"team" => $team,
			"adminEmail" => false,
			"howHeardMethod" => $howHeardMethod,
			"howHeardOther" => $howHeardOther,
			"paymentMethod" => Includes_PaymentMethods::getMethodByOrdinal($team->getPaymentMethod()),
			"isComment" => $isComment,
			"captain" => $team->getCaptain(),
			"returningString" => $returningString
		];
		
		$body = $this->templateEngine->render($regEmailTemplate->getTemplateLink(), $params);
		
		$emailController->createAndSendEmail(
				$regEmailTemplate->getEmailType(), 
				$subject, 
				$body, 
				$team->getCaptain()->getEmail(), 
				$regEmailTemplate->getFromName(),
				$regEmailTemplate->getFromAddress(), 
				null, 
				null
		);
		
		$params["adminEmail"] = true;
		$adminBody = $this->templateEngine->render($regEmailTemplate->getTemplateLink(), $params);
		
		$emailController->createAndSendEmail(
				$regEmailTemplate->getEmailType(), 
				$adminSubject, 
				$adminBody, 
				implode(",", $regEmailTemplate->getToAddresses()), 
				$regEmailTemplate->getFromName(),
				$regEmailTemplate->getFromAddress(), 
				null, 
				null
		);
	}

	function sendRegistrationEmailGroup(array $groupMembers, Models_Individual $group) {
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);

		$captain = $groupMembers[0];
		$leagueChoice = Models_League::withID($this->db, $this->logger, $group->getPreferredLeagueID());
		$payment = $group->getPaymentMethod();

		$isComment = $captain->getRegistrationComment() != null && !empty($captain->getRegistrationComment());
		$howHeardMethod = $captain->getHowHeardMethod() > 0 ? Includes_HeardAboutUsMethods::getMethodByOrdinal($captain->getHowHeardMethod()) : null;
		$howHeardOther = $captain->getHowHeardMethod() > 0 ? $captain->getHowHeardOtherText() : '';

		$subject = 'Registration Confirmation - ' . (sizeof($groupMembers) > 1 ? 'Small Group' : 'Individual') . ' - ' . $leagueChoice->getRegistrationFormattedNameGroup();
		
		$adminSubject = 'Reg - ' . ($isComment ? 'Com - ' : '') . (sizeof($groupMembers) > 1 ? 'Small Group' : 'Individual') . ' - ' . $leagueChoice->getRegistrationFormattedNameGroup();

		$regEmailTemplate = Includes_EmailTypes::groupRegistered();
		
		$params = [
			"groupMembers" => $groupMembers,
			"group" => $group,
			"leagueChoice" => $leagueChoice,
			"adminEmail" => false,
			"howHeardMethod" => $howHeardMethod,
			"howHeardOther" => $howHeardOther,
			"paymentMethod" => Includes_PaymentMethods::getMethodByOrdinal($payment),
			"isComment" => $isComment,
			"captain" => $captain
		];
		
		$body = $this->templateEngine->render($regEmailTemplate->getTemplateLink(), $params);
		
		$emailController->createAndSendEmail(
				$regEmailTemplate->getEmailType(), 
				$subject, 
				$body, 
				$captain->getEmail(), 
				$regEmailTemplate->getFromName(),
				$regEmailTemplate->getFromAddress(), 
				null, 
				null
		);
		
		$params["adminEmail"] = true;
		$adminBody = $this->templateEngine->render($regEmailTemplate->getTemplateLink(), $params);
		
		$emailController->createAndSendEmail(
				$regEmailTemplate->getEmailType(), 
				$adminSubject, 
				$adminBody, 
				implode(",", $regEmailTemplate->getToAddresses()), 
				$regEmailTemplate->getFromName(),
				$regEmailTemplate->getFromAddress(), 
				null, 
				null
		);
	}
	
	function sendTeamUnregisteredEmail(Models_Team $team) {
		
		$comment = '';

		$title = '<tr><th colspan="3" align="center">Perpetual Motion Online Registration System<BR>-- Team Unregistered Themselves --</th></tr>';

		$secondary = '<tr><td>Team Unregistered for <br><b>' . $team->getLeague()->getSport()->getName() 
				. ' - ' . $team->getLeague()->getFormattedName() . '.</b></td></tr>';
		
		$regIDLine = '<tr><td align="center" colspan="3"><b>Team Name:</b> ' . $team->getName() . '</td></tr>'
				. '<tr><td colspan="3" align="center">Team Number: ' . $team->getId() . '</td></tr>';	
		
		$subject = 'Unreg - '. $team->getName();

		if($team->getRegistrationComment() != null && !empty($team->getRegistrationComment())) {
			$subject = 'Unreg - Com - Team  - ' . $team->getName();
			$comment = '<tr><td colspan="3" align="center">Comment: ' . $team->getRegistrationComment() . '</td></tr>';
		}
		$message = '<html><body><table align="center" cellspacing="2" cellpadding="2" style="font:10px;">'
				. $title . $secondary . $regIDLine . $comment 
				. '</table></body></html>';
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$deRegEmailTemplate = Includes_EmailTypes::teamDeregistered();
		
		$emailController->createAndSendEmail(
				$deRegEmailTemplate->getEmailType(), 
				$subject, 
				$message, 
				implode(",", $deRegEmailTemplate->getToAddresses()),
				$deRegEmailTemplate->getFromName(),
				$deRegEmailTemplate->getFromAddress(), 
				null, 
				null
		);
	}

	function sendWaiverEmails(Models_Team $team) {
		
		$toSend = array();

		$body = $this->templateEngine->render('email-waiver', [
				"team" => $team
		]);

		$subject = 'Online Waiver - ' . $team->getName();

		if($team->getPlayers() != null && !empty($team->getPlayers())) {
			foreach($team->getPlayers() as $curPlayer) {
				if (filter_var($curPlayer->getEmail(), FILTER_VALIDATE_EMAIL) 
						&& !in_array($curPlayer->getEmail(), $toSend)) {
					array_push($toSend, $curPlayer->getEmail());
				}
			}
		}
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$emailTemplate = Includes_EmailTypes::sendWaiver();
		
		$emailController->createAndSendEmail(
				$emailTemplate->getEmailType(), 
				$subject, 
				$body, 
				null,
				$emailTemplate->getFromName(),
				$emailTemplate->getFromAddress(), 
				null, 
				implode(',', $toSend)
		);
		
		//sendEmailsBcc($toSend,'info@perpetualmotion.org', $subject, $body);
	}

	/* Currently set to run for every player instead of using an array of players and running it once. Should probably be changed later to the latter */
	function sendWaiverEmailsGroup(array $groupMembers) {

		$toSend = array();

		$body = $this->templateEngine->render('email-waiver-group', []);

		$subject = 'Online Waiver - Free Agent';

		foreach($groupMembers as $curPlayer) {
			if($curPlayer != null && $curPlayer->getEmail() != null) {
				if(filter_var($curPlayer->getEmail(), FILTER_VALIDATE_EMAIL) && !in_array($curPlayer->getEmail(), $toSend)) {
					$toSend[] = $curPlayer->getEmail();
				}
			}
		}
		
		$emailController = new Controllers_EmailsController($this->db, $this->logger);
		$emailTemplate = Includes_EmailTypes::sendWaiver(); /* sendWaiver() in emailtypes calls email-waiver.php, not email-waiver-group.php. So create a new type sendWaiverGroup() that contains the latter for templateLink if this stops working correctly */
		
		$emailController->createAndSendEmail(
				$emailTemplate->getEmailType(), 
				$subject, 
				$body, 
				null,
				$emailTemplate->getFromName(),
				$emailTemplate->getFromAddress(), 
				null, 
				implode(',', $toSend)
		);
		
		//sendEmailsBcc($toSend,'info@perpetualmotion.org', $subject, $body);
	}
}
