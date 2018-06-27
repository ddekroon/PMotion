<?php

class Controllers_GroupsController extends Controllers_Controller {

	public function insertGroup($request)
	{
		$allPostVars = $request->getParsedBody();
		// $this->logger->critical("Player found!"); // For testing - Kyle


		$sport = Models_Sport::withID($this->db, $this->logger, $allPostVars['sportID']);

		if(isset($allPostVars['playerFirstName_1'])) {
			$stmt = $this->db->query("SELECT MAX(individual_small_group_id) as highestGroupNum FROM " . Includes_DBTableNames::individualsTable);

			if(($row = $stmt->fetch()) != false) {
				$newGroupID = $row['highestGroupNum'] + 1;
			}
		}

		for($i = 0; $i < $sport->getNumPlayerInputsForRegistration(); $i++) {
			// $playerID = $allPostVars['playerID_' . $i] // Is this even doing anything?

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
				$curPlayer->setHowHeardMethod(isset($allPostVars['capHowHeardMethod']) ? $allPostVars['capHowHeardMethod'] : '');
				$curPlayer->setHowHeardOtherText(isset($allPostVars['capHowHeardMethodOther']) ? $allPostVars['capHowHeardMethodOther'] : '');
				// $managerID = $curPlayer->getId(); // Fo
			}

			$curIndiv = Models_Individual::withRow($this->db, $this->logger, []);

			
			$curIndiv->setPhoneNumber($allPostVars['playerPhoneNumber_' . $i]);
			$curIndiv->setPreferredLeagueID($allPostVars['leagueID']);
			// $curIndiv->setDateCreated(?);
			$curIndiv->setIsFinalized(true);
			// $curIndiv->setManagedByID($managerID); // Managed by USER ID not player ID, so this is not needed to be anything but 0
			$curIndiv->setGroupID($newGroupID);
			$curIndiv->setPaymentMethod($allPostVars['groupPaymentMethod']);

			// TODO: how to do dateCreated. Does table do it auto?

			// TODO: Set note (I think under player)
			$newPlayer = $curPlayer->getFirstName();

			if(isset($newPlayer)) {
				$curPlayer->saveOrUpdate(); //Works, but commented until individual is working so people don't keep getting added to player table on tests
				$curIndiv->setPlayerID($curPlayer->getId()); // Not sure this will grab ID correctly
				$curIndiv->save();
			}
			//$curIndiv->save();

			// $players[] = $curPlayer; // Pretty sure this isn't needed as we're not adding players to something, just individuals to players
		}

		return "Your group has been registered!";

	}

}
