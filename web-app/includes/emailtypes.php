<?php

abstract class Includes_EmailTypes {
	
	public static function userRegistered() {
		return Includes_EmailTemplate::createEmailTemplate(
				"User Registered",
				[], 
				"User Registered", 
				"email-user-registration", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function passwordResetRequest() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Password Reset Request",
				[], 
				"Perpetual Motion Password Reset Request", 
				"email-request-password-reset", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function passwordResetSuccess() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Password Reset Success",
				[], 
				"Perpetual Motion Password Reset Success", 
				"email-password-reset-success", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function tournamentTeamRegistered() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Tournament Team Registered",
				[
					'dave@perpetualmotion.org',
					'brayden@perpetualmotion.org',
					'herman@perpetualmotion.org'
				], 
				"Tournament Team Registered", 
				"email-tournament-team-registration", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function teamRegistered() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Team Registered",
				[
					'dave@perpetualmotion.org',
					'brayden@perpetualmotion.org',
					'herman@perpetualmotion.org',
					'derek@perpetualmotion.org'
				], 
				"Team Registered", 
				"email-team-registration", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function teamDeregistered() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Team Deregistered",
				[
					'dave@perpetualmotion.org',
					'brayden@perpetualmotion.org',
					'herman@perpetualmotion.org'
				], 
				"Team Deregistered", 
				"email-team-registration", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function groupRegistered() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Group Registered",
				[
					'dave@perpetualmotion.org',
					'brayden@perpetualmotion.org',
					'herman@perpetualmotion.org'
				], 
				"Small Group Registered", 
				"email-group-registration", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}
	
	public static function sendWaiver() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Waiver",
				[], 
				"Perpetual Motion Waiver", 
				"email-waiver", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}

	public static function scoreSubmission() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Score Submission",
				[
					'dave@perpetualmotion.org',
					'derek@perpetualmotion.org'
				], 
				"Score Submission", 
				"email-score-submission", 
				"Perpetual Motion Score Reporter",
				"scores@perpetualmotion.org"
		);
	}

	public static function controlPanelMailer() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Control Panel Mail",
				[
					'dave@perpetualmotion.org'
				], 
				"Control Panel Email", 
				"email-control-panel", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	}

	//DD Feb 10, 2018: What is the eff is this function for? What email could possibly getting sent out here?
	public static function controlPanelTournamentLeague() {
		return Includes_EmailTemplate::createEmailTemplate(
				"Control Panel Tournament League",
				[
					'dave@perpetualmotion.org'
				], 
				"Control Panel Tournament League", 
				"email-control-panel-tournament-league", 
				"Perpetual Motion Information",
				"info@perpetualmotion.org"
		);
	} 
}

