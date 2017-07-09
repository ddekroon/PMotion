
ALTER TABLE `data_perpetualmotion`.`auth_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`dates_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`faq_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`leagues_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`players_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`schedule_variables_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`score_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`seasons_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`spirit_scores_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`sports_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`standings_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`teams_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`tournament_players` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`tournament_teams` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`tournaments` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`user_history_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`users_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`venues_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `data_perpetualmotion`.`teampicturearchive` CHARACTER SET = utf8 , ENGINE = InnoDB ;

-- LEAGUES

delete l FROM data_perpetualmotion.leagues_dbtable l 
left JOIN seasons_dbtable on l.league_season_id = season_id 
where season_id is null;

ALTER TABLE `data_perpetualmotion`.`leagues_dbtable` 
ADD INDEX `FK_League_Season_idx` (`league_season_id` ASC);
ALTER TABLE `data_perpetualmotion`.`leagues_dbtable` 
ADD CONSTRAINT `FK_League_Season`
  FOREIGN KEY (`league_season_id`)
  REFERENCES `data_perpetualmotion`.`seasons_dbtable` (`season_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`leagues_dbtable` 
ADD INDEX `FK_League_Sport_idx` (`league_sport_id` ASC);
ALTER TABLE `data_perpetualmotion`.`leagues_dbtable` 
ADD CONSTRAINT `FK_League_Sport`
  FOREIGN KEY (`league_sport_id`)
  REFERENCES `data_perpetualmotion`.`sports_dbtable` (`sport_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- TEAMS


ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
CHANGE COLUMN `team_league_id` `team_league_id` INT(11) NULL DEFAULT NULL ;

update data_perpetualmotion.teams_dbtable team 
left JOIN leagues_dbtable on team.team_league_id = league_id 
set team_league_id = null 
where league_id is null;

ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
ADD INDEX `FK_Team_League_idx` (`team_league_id` ASC);
ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
ADD CONSTRAINT `FK_Team_League`
  FOREIGN KEY (`team_league_id`)
  REFERENCES `data_perpetualmotion`.`leagues_dbtable` (`league_id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
DROP COLUMN `team_captain_id`;

ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
CHANGE COLUMN `team_managed_by_user_id` `team_managed_by_user_id` INT(11) NULL DEFAULT NULL;

update data_perpetualmotion.teams_dbtable team
left JOIN users_dbtable on team.team_managed_by_user_id = user_id 
set team.team_managed_by_user_id = NULL
where user_id is null;

ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
ADD INDEX `FK_Team_Manager_idx` (`team_managed_by_user_id` ASC);
ALTER TABLE `data_perpetualmotion`.`teams_dbtable` 
ADD CONSTRAINT `FK_Team_Manager`
  FOREIGN KEY (`team_managed_by_user_id`)
  REFERENCES `data_perpetualmotion`.`users_dbtable` (`user_id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

-- PLAYERS

delete player.* FROM data_perpetualmotion.players_dbtable player
left JOIN teams_dbtable on player.player_team_id = team_id 
where team_id is null;

ALTER TABLE `data_perpetualmotion`.`players_dbtable` 
ADD INDEX `FK_Player_Team_idx` (`player_team_id` ASC);
ALTER TABLE `data_perpetualmotion`.`players_dbtable` 
ADD CONSTRAINT `FK_Player_Team`
  FOREIGN KEY (`player_team_id`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- INDIVIDUALS

delete ind.* FROM data_perpetualmotion.individuals_dbtable ind
left JOIN players_dbtable on ind.individual_player_id = player_id 
where player_id is null;

ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD INDEX `FK_Individual_Player_idx` (`individual_player_id` ASC);
ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD CONSTRAINT `FK_Individual_Player`
  FOREIGN KEY (`individual_player_id`)
  REFERENCES `data_perpetualmotion`.`players_dbtable` (`player_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
CHANGE COLUMN `individual_preferred_league_id` `individual_preferred_league_id` INT(11) NULL DEFAULT NULL ;

update data_perpetualmotion.individuals_dbtable ind
left JOIN leagues_dbtable on ind.individual_preferred_league_id = league_id 
set ind.individual_preferred_league_id = NULL
where league_id is null;

ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD INDEX `FK_Individual_PreferredLeague_idx` (`individual_preferred_league_id` ASC);
ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD CONSTRAINT `FK_Individual_PreferredLeague`
  FOREIGN KEY (`individual_preferred_league_id`)
  REFERENCES `data_perpetualmotion`.`leagues_dbtable` (`league_id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
CHANGE COLUMN `individual_managed_by_user_id` `individual_managed_by_user_id` INT(11) NULL DEFAULT NULL;

update data_perpetualmotion.individuals_dbtable ind
left JOIN users_dbtable on ind.individual_managed_by_user_id = user_id 
set ind.individual_managed_by_user_id = NULL
where user_id is null;

ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD INDEX `FK_Individual_ManagedByUser_idx` (`individual_managed_by_user_id` ASC);
ALTER TABLE `data_perpetualmotion`.`individuals_dbtable` 
ADD CONSTRAINT `FK_Individual_ManagedByUser`
  FOREIGN KEY (`individual_managed_by_user_id`)
  REFERENCES `data_perpetualmotion`.`users_dbtable` (`user_id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

-- REGISTRATION

ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD INDEX `FK_RegComment_User_idx` (`registration_comment_user_id` ASC);
ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD CONSTRAINT `FK_RegComment_User`
  FOREIGN KEY (`registration_comment_user_id`)
  REFERENCES `data_perpetualmotion`.`users_dbtable` (`user_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
CHANGE COLUMN `registration_comment_team_id` `registration_comment_team_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `registration_comment_individual_id` `registration_comment_individual_id` INT(11) NULL DEFAULT NULL ;

update data_perpetualmotion.registration_comments_dbtable rc
left JOIN teams_dbtable on team_id = rc.registration_comment_team_id 
set registration_comment_team_id = null
where team_id is null;

ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD INDEX `FK_RegComment_Team_idx` (`registration_comment_team_id` ASC);
ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD CONSTRAINT `FK_RegComment_Team`
  FOREIGN KEY (`registration_comment_team_id`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

update data_perpetualmotion.registration_comments_dbtable rc
left JOIN individuals_dbtable on individual_id = rc.registration_comment_individual_id 
set registration_comment_individual_id = null
where individual_id is null;

ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD INDEX `FK_RegComment_Individual_idx` (`registration_comment_individual_id` ASC);
ALTER TABLE `data_perpetualmotion`.`registration_comments_dbtable` 
ADD CONSTRAINT `FK_RegComment_Individual`
  FOREIGN KEY (`registration_comment_individual_id`)
  REFERENCES `data_perpetualmotion`.`individuals_dbtable` (`individual_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- SCHEDULED MATCHES

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_TeamOne_idx` (`scheduled_match_team_id_1` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_TeamOne`
  FOREIGN KEY (`scheduled_match_team_id_1`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_TeamTwo_idx` (`scheduled_match_team_id_2` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_TeamTwo`
  FOREIGN KEY (`scheduled_match_team_id_2`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

delete sm FROM data_perpetualmotion.scheduled_matches_dbtable sm 
left JOIN dates_dbtable on date_id = scheduled_match_date_id 
where date_id is null;

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_Date_idx` (`scheduled_match_date_id` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_Date`
  FOREIGN KEY (`scheduled_match_date_id`)
  REFERENCES `data_perpetualmotion`.`dates_dbtable` (`date_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

delete sm FROM data_perpetualmotion.scheduled_matches_dbtable sm 
left JOIN venues_dbtable on venue_id = sm.scheduled_match_field_id 
where venue_id is null;

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_Venue_idx` (`scheduled_match_field_id` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_Venue`
  FOREIGN KEY (`scheduled_match_field_id`)
  REFERENCES `data_perpetualmotion`.`venues_dbtable` (`venue_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_League_idx` (`scheduled_match_league_id` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_League`
  FOREIGN KEY (`scheduled_match_league_id`)
  REFERENCES `data_perpetualmotion`.`leagues_dbtable` (`league_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- SCORE SUBMISSIONS

delete ss FROM data_perpetualmotion.score_submissions_dbtable ss 
left JOIN teams_dbtable on team_id = ss.score_submission_team_id 
where team_id is null;

ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD INDEX `FK_ScoreSubmission_Team_idx` (`score_submission_team_id` ASC);
ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD CONSTRAINT `FK_ScoreSubmission_Team`
  FOREIGN KEY (`score_submission_team_id`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
CHANGE COLUMN `score_submission_opp_team_id` `score_submission_opp_team_id` INT(11) NULL DEFAULT NULL ;

update data_perpetualmotion.score_submissions_dbtable ss 
left JOIN teams_dbtable on team_id = ss.score_submission_opp_team_id 
set score_submission_opp_team_id = null
where team_id is null;

ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD INDEX `FK_ScoreSubmission_OppTeam_idx` (`score_submission_opp_team_id` ASC);
ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD CONSTRAINT `FK_ScoreSubmission_OppTeam`
  FOREIGN KEY (`score_submission_opp_team_id`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

delete ss FROM data_perpetualmotion.score_submissions_dbtable ss 
left JOIN dates_dbtable on date_id = ss.score_submission_date_id 
where date_id is null;

ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD INDEX `FK_ScoreSubmission_Date_idx` (`score_submission_date_id` ASC);
ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
ADD CONSTRAINT `FK_ScoreSubmission_Date`
  FOREIGN KEY (`score_submission_date_id`)
  REFERENCES `data_perpetualmotion`.`dates_dbtable` (`date_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`score_submissions_dbtable` 
CHANGE COLUMN `score_submission_ignored` `score_submission_ignored` TINYINT(1) NOT NULL ,
CHANGE COLUMN `score_submission_dont_show` `score_submission_dont_show` TINYINT(1) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `score_submission_is_phantom` `score_submission_is_phantom` TINYINT(1) NOT NULL ;


-- Score Submission Comments

delete ssc FROM data_perpetualmotion.score_comments_dbtable ssc 
left JOIN score_submissions_dbtable as ss on ss.score_submission_id = ssc.comment_score_submission_id 
where ss.score_submission_id is null;

ALTER TABLE `data_perpetualmotion`.`score_comments_dbtable` 
ADD INDEX `FK_ScoreComment_ScoreSubmission_idx` (`comment_score_submission_id` ASC);
ALTER TABLE `data_perpetualmotion`.`score_comments_dbtable` 
ADD CONSTRAINT `FK_ScoreComment_ScoreSubmission`
  FOREIGN KEY (`comment_score_submission_id`)
  REFERENCES `data_perpetualmotion`.`score_submissions_dbtable` (`score_submission_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- Spirit Scores

delete sp FROM data_perpetualmotion.spirit_scores_dbtable sp 
left JOIN score_submissions_dbtable as ss on ss.score_submission_id = sp.spirit_score_score_submission_id 
where ss.score_submission_id is null;

ALTER TABLE `data_perpetualmotion`.`spirit_scores_dbtable` 
ADD INDEX `FK_SpirtScore_ScoreSubmission_idx` (`spirit_score_score_submission_id` ASC);
ALTER TABLE `data_perpetualmotion`.`spirit_scores_dbtable` 
ADD CONSTRAINT `FK_SpirtScore_ScoreSubmission`
  FOREIGN KEY (`spirit_score_score_submission_id`)
  REFERENCES `data_perpetualmotion`.`score_submissions_dbtable` (`score_submission_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`spirit_scores_dbtable` 
CHANGE COLUMN `spirit_score_ignored` `spirit_score_ignored` TINYINT(1) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `spirit_score_dont_show` `spirit_score_dont_show` TINYINT(1) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `spirit_score_is_admin_addition` `spirit_score_is_admin_addition` TINYINT(1) NOT NULL DEFAULT '0' ;


-- DATES

ALTER TABLE `data_perpetualmotion`.`dates_dbtable` 
ADD INDEX `FK_Date_Sport_idx` (`date_sport_id` ASC);
ALTER TABLE `data_perpetualmotion`.`dates_dbtable` 
ADD CONSTRAINT `FK_Date_Sport`
  FOREIGN KEY (`date_sport_id`)
  REFERENCES `data_perpetualmotion`.`sports_dbtable` (`sport_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

delete d FROM data_perpetualmotion.dates_dbtable d 
left JOIN seasons_dbtable on season_id = d.date_season_id 
where season_id is null;

ALTER TABLE `data_perpetualmotion`.`dates_dbtable` 
ADD INDEX `FK_Date_Season_idx` (`date_season_id` ASC);
ALTER TABLE `data_perpetualmotion`.`dates_dbtable` 
ADD CONSTRAINT `FK_Date_Season`
  FOREIGN KEY (`date_season_id`)
  REFERENCES `data_perpetualmotion`.`seasons_dbtable` (`season_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- USER HISTORY

delete uh FROM data_perpetualmotion.user_history_dbtable uh 
left JOIN users_dbtable on user_id = uh.user_history_user_id
where user_id is null;

ALTER TABLE `data_perpetualmotion`.`user_history_dbtable` 
ADD INDEX `FK_UserHistory_User_idx` (`user_history_user_id` ASC);
ALTER TABLE `data_perpetualmotion`.`user_history_dbtable` 
ADD CONSTRAINT `FK_UserHistory_User`
  FOREIGN KEY (`user_history_user_id`)
  REFERENCES `data_perpetualmotion`.`users_dbtable` (`user_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- AUTH

ALTER TABLE `data_perpetualmotion`.`auth_dbtable` 
ADD INDEX `FK_Auth_User_idx` (`userId` ASC);
ALTER TABLE `data_perpetualmotion`.`auth_dbtable` 
ADD CONSTRAINT `FK_Auth_User`
  FOREIGN KEY (`userId`)
  REFERENCES `data_perpetualmotion`.`users_dbtable` (`user_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- TEAM PICTURES

ALTER TABLE `data_perpetualmotion`.`teampicturearchive` 
CHANGE COLUMN `picture_team_id` `picture_team_id` INT(11) NULL DEFAULT NULL;

update data_perpetualmotion.teampicturearchive tp
left JOIN teams_dbtable on team_id = tp.picture_team_id 
set tp.picture_team_id = null
where team_id is null;

ALTER TABLE `data_perpetualmotion`.`teampicturearchive` 
ADD INDEX `FK_PicArchive_Team_idx` (`picture_team_id` ASC);
ALTER TABLE `data_perpetualmotion`.`teampicturearchive` 
ADD CONSTRAINT `FK_PicArchive_Team`
  FOREIGN KEY (`picture_team_id`)
  REFERENCES `data_perpetualmotion`.`teams_dbtable` (`team_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `data_perpetualmotion`.`teampicturearchive`
ADD UNIQUE INDEX `UNIQUE_TeamPicture_Selector` (`identifier` ASC);

-- VENUES

ALTER TABLE `data_perpetualmotion`.`venues_dbtable` 
ADD INDEX `FK_Venue_Sport_idx` (`venue_sport_id` ASC);
ALTER TABLE `data_perpetualmotion`.`venues_dbtable` 
ADD CONSTRAINT `FK_Venue_Sport`
  FOREIGN KEY (`venue_sport_id`)
  REFERENCES `data_perpetualmotion`.`sports_dbtable` (`sport_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- Tournament Teams

ALTER TABLE `data_perpetualmotion`.`tournament_teams` 
ADD INDEX `FK_TournamentTeam_Tournament_idx` (`tournament_team_tournament_id` ASC);
ALTER TABLE `data_perpetualmotion`.`tournament_teams` 
ADD CONSTRAINT `FK_TournamentTeam_Tournament`
  FOREIGN KEY (`tournament_team_tournament_id`)
  REFERENCES `data_perpetualmotion`.`tournaments` (`tournament_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- Tournament Players

delete tp FROM data_perpetualmotion.tournament_players tp 
left JOIN tournaments on tournament_id = tp.tournament_player_tournament_id
where tournament_id is null;

ALTER TABLE `data_perpetualmotion`.`tournament_players` 
ADD INDEX `FK_TournamentPlayer_Tournament_idx` (`tournament_player_tournament_id` ASC);
ALTER TABLE `data_perpetualmotion`.`tournament_players` 
ADD CONSTRAINT `FK_TournamentPlayer_Tournament`
  FOREIGN KEY (`tournament_player_tournament_id`)
  REFERENCES `data_perpetualmotion`.`tournaments` (`tournament_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
