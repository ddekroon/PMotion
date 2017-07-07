<?php

abstract class Includes_WhoGetsEmailed {
	
	public static function tourneyReg() {
		$email[] = 'dave@perpetualmotion.org';
		$email[] = 'terry@perpetualmotion.org';
		$email[] = 'nick@perpetualmotion.org';
		return $email;
	}

	public static function teamReg() {
		$email[] = 'dave@perpetualmotion.org';
		$email[] = 'terry@perpetualmotion.org';
		$email[] = 'nick@perpetualmotion.org';
		return $email;
	}

	public static function teamUnreg() {
		$email[] = 'dave@perpetualmotion.org';
		$email[] = 'terry@perpetualmotion.org';
		$email[] = 'nick@perpetualmotion.org';
		return $email;
	}

	public static function groupReg() {
		$email[] = 'dave@perpetualmotion.org';
		$email[] = 'terry@perpetualmotion.org';
		$email[] = 'nick@perpetualmotion.org';
		return $email;
	}

	public static function scoreSubmission() {
		$email[] = 'dave@perpetualmotion.org';
		$email[] = 'terry@perpetualmotion.org';
		$email[] = 'nick@perpetualmotion.org';
		return $email;
	} 

	public static function controlPanelMailer() {
		$email[] = 'dave@perpetualmotion.org';
		return $email;	
	}

	public static function controlPanelTournamentLeague() {
		$email[] = 'dave@perpetualmotion.org';
		return $email;	
	}
}

