<?php

/* Created by Kyle Conrad - Summer 2018 */

class Controllers_GroupsController extends Controllers_Controller {

	public function insertGroup($request)
	{
		$allPostVars = $request->getParsedBody();
		// $this->logger->critical("Player found!"); // For testing - Kyle


		$sport = Models_Sport::withID($this->db, $this->logger, $allPostVars['sportID']);

		/* If there's multiple individuals it becomes a group and this assigns the groupID for the new group - Kyle */
		if(!empty($allPostVars['playerFirstName_1'])) {
			$stmt = $this->db->query("SELECT MAX(individual_small_group_id) as highestGroupNum FROM " . Includes_DBTableNames::individualsTable);

			if(($row = $stmt->fetch()) != false) {
				$newGroupID = $row['highestGroupNum'] + 1;
			}
		}
		else {
			$newGroupID = 0;
		}

		for($i = 0; $i < $sport->getNumPlayerInputsForRegistration(); $i++) {

			$curPlayer = Models_Player::withRow($this->db, $this->logger, []);
			
			$curPlayer->setFirstName($allPostVars['playerFirstName_' . $i]);
			$curPlayer->setLastName($allPostVars['playerLastName_' . $i]);
			$curPlayer->setEmail($allPostVars['playerEmail_' . $i]);
			$curPlayer->setPhoneNumber($allPostVars['playerPhoneNumber_' . $i]);
			$curPlayer->setGender($allPostVars['playerGender_' . $i]);
			$curPlayer->setSkillLevel($allPostVars['playerSkill_' . $i]);
			$curPlayer->setIsIndividual(true);

			if($i == 0)
			{
				$curPlayer->setHowHeardMethod(isset($allPostVars['capHowHeardMethod']) ? $allPostVars['capHowHeardMethod'] : 0);
				$curPlayer->setHowHeardOtherText(isset($allPostVars['capHowHeardMethodOther']) ? $allPostVars['capHowHeardMethodOther'] : '');
				$groupNote = '';

				/* Creating the note from comments section including 2nd and 3rd choice leagues - Kyle */
				for($j = 2; $j <= 3; $j++) {
					if(($allPostVars['leagueID' . $j]) != -1) {
						$prefLeagueID = $allPostVars['leagueID' . $j];

						$sql = "SELECT * FROM " . Includes_DBTableNames::leaguesTable . " WHERE league_id = '$prefLeagueID'";

						$stmt = $this->db->query($sql);

						if(($row = $stmt->fetch()) != false) {
							$otherLeague = Models_League::withRow($this->db, $this->logger, $row);
						}
						$prefLeagueName = $otherLeague->getRegistrationFormattedName();

						$groupNote .= "PL$j-" . $prefLeagueName . ' ';
					}
				}
				$groupNote .= (isset($allPostVars['groupComments']) ? $allPostVars['groupComments'] : '');
				
				$curPlayer->setNote($groupNote);
				$curPlayer->getRegistrationComment()->setComment($groupNote);
			}

			$curIndiv = Models_Individual::withRow($this->db, $this->logger, []);

			// dateCreated and individualID are both automatically assigned by db upon submission, so they aren't mentioned here

			$curIndiv->setPhoneNumber($allPostVars['playerPhoneNumber_' . $i]);
			$curIndiv->setPreferredLeagueID($allPostVars['leagueID']);
			$curIndiv->setIsFinalized(true);
			// $curIndiv->setManagedByID($managerID); /* Managed by USER ID not player ID, so this is not needed to be anything but 0 unless user-based individual registration is implemented later */
			$curIndiv->setGroupID($newGroupID);
			$curIndiv->setPaymentMethod($allPostVars['groupPaymentMethod']);
			$curIndiv->setHowHeardMethod(0); // This is stored but unused in this db table. It's stored and used via player table
			$curIndiv->setHowHeardOtherText(''); // Same note as previous

			$newPlayer = $curPlayer->getFirstName();

			if(isset($newPlayer)) {
				$curPlayer->saveOrUpdate();
				$this->insertPlayerAddressDB($curPlayer);

				$curIndiv->setPlayerID($curPlayer->getId());
				$curIndiv->save();

				if($i == 0) {
					$curPlayer->getRegistrationComment()->saveOrUpdate();
				}
			}
		}

		return "Your group has been registered!";

	}

	/* This function is entirely from teamscontroller.php and works here without needing any changes */
	function insertPlayerAddressDB($player) {

		if(filter_var($player->getEmail(), FILTER_VALIDATE_EMAIL)) {
			
			$stmt = $this->db->query("SELECT count(*) as numEmails FROM " . Includes_DBTableNames::addressesTable . " WHERE EmailAddress = '" . $player->getEmail() . "'");

			if(($row = $stmt->fetch()) != false) {
				if($row['numEmails'] == 0) {
					$newEmail = Models_EmailAddress::withID($this->db, $this->logger, -1);
					$newEmail->setFirstName($player->getFirstName());
					$newEmail->setLastName($player->getLastName());
					$newEmail->setEmail($player->getEmail());
					
					$newEmail->save();
				}
			}
		}
	}

}
