<?php

class Controllers_GroupsController extends Controllers_Controller {

	public function insertGroup($request)
	{
		$allPostVars = $request->getParsedBody();
		// $this->logger->critical("Player found!"); // For testing - Kyle


		$sport = Models_Sport::withID($this->db, $this->logger, $allPostVars['sportID']);

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
			}

			$curIndiv = Models_Individual::withRow($this->db, $this->logger, []);

			// dateCreated and individualID are both automatically assigned by db upon submission, so they aren't mentioned here

			$curIndiv->setPhoneNumber($allPostVars['playerPhoneNumber_' . $i]);
			$curIndiv->setPreferredLeagueID($allPostVars['leagueID']);
			$curIndiv->setIsFinalized(true);
			// $curIndiv->setManagedByID($managerID); // Managed by USER ID not player ID, so this is not needed to be anything but 0
			$curIndiv->setGroupID($newGroupID);
			$curIndiv->setPaymentMethod($allPostVars['groupPaymentMethod']);
			$curIndiv->setHowHeardMethod(0); // This is unused in db. It's stored and used via player table
			$curIndiv->setHowHeardOtherText(''); // Same as previous

			// TODO: Set note (I think under player)
			$newPlayer = $curPlayer->getFirstName();

			if(isset($newPlayer)) {
				$curPlayer->saveOrUpdate();
				$curIndiv->setPlayerID($curPlayer->getId());
				$curIndiv->save();
			}
		}

		return "Your group has been registered!";

	}

}
