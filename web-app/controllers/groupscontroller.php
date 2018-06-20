<?php

class Controllers_GroupsController extends Controllers_Controller {

	public function insertGroup($group, $request)
	{
		$allPostVars = $request->getParsedBody();

		$sport = Models_Sport::withID($this->db, $this->logger, $allPostVars['sportID']);

		for(i = 0; $i < $sport->getNumPlayerInputsForRegistration(); $i++) {
			// $playerID = $allPostVars['playerID_' . $i] // Is this even doing anything?

			$curPlayer = Models_Player::withRow($this->db, $this->logger, []);
			
			$curPlayer->setFirstName($allPostVars['playerFirstName_' . $i]);
			$curPlayer->setLastName($allPostVars['playerLastName_' . $i]);
			$curPlayer->setEmail($allPostVars['playerEmail_' . $i]);
			$curPlayer->setPhoneNumber($allPostVars['playerPhoneNumber_' . $i]);
			$curPlayer->setGender($allPostVars['playerGender_' . $i]);
			$curPlayer->setSkillLevel($allPostVars['playerSkillLevel_' . $i]);
			$curPlayer->setIsIndividual(true);

			if($i == 0)
			{
				$curPlayer->setHowHeardMethod(isset($allPostVars['capHowHeardMethod']) ? $allPostVars['capHowHeardMethod'] : '');
				$curPlayer->setHowHeardOtherText(isset($allPostVars['capHowHeardMethodOther']) ? $allPostVars['capHowHeardMethodOther'] : '');
			}

			$curIndiv = Models_Individual::withRow($this->db, $this->logger, []);

			$curIndiv->setPlayerID($curPlayer->getId());
			$curIndiv->setPhoneNumber($allPostVars['playerPhoneNumber_']);
			$curIndiv->setPreferredLeagueID($allPostVars['leagueID']);

			// TODO: add group id if numPlayers > 1. Find next groupID from table max


			// TODO: Set note (I think under player)

			// $players[] = $curPlayer; // Pretty sure this isn't needed as we're not adding players to something, just individuals to players
		}


		return "Test successful";

	}


?>