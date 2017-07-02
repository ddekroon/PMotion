
ALTER TABLE `auth_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `dates_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `faq_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `individuals_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `leagues_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `players_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `registration_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `schedule_variables_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `scheduled_matches_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `score_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `seasons_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `spirit_scores_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `sports_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `standings_comments_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `teams_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `tournament_players` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `tournament_teams` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `tournaments` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `user_history_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `users_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `venues_dbtable` CHARACTER SET = utf8 , ENGINE = InnoDB ;
ALTER TABLE `teampicturearchive` CHARACTER SET = utf8 , ENGINE = InnoDB ;

ALTER TABLE `scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_TeamOne_idx` (`scheduled_match_team_id_1` ASC);
ALTER TABLE `scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_TeamOne`
  FOREIGN KEY (`scheduled_match_team_id_1`)
  REFERENCES `teams_dbtable` (`team_id`)
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

--delete sm FROM data_perpetualmotion.scheduled_matches_dbtable sm 
--left JOIN dates_dbtable on date_id = scheduled_match_date_id 
--where date_id is null;

ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD INDEX `FK_ScheduledMatch_Date_idx` (`scheduled_match_date_id` ASC);
ALTER TABLE `data_perpetualmotion`.`scheduled_matches_dbtable` 
ADD CONSTRAINT `FK_ScheduledMatch_Date`
  FOREIGN KEY (`scheduled_match_date_id`)
  REFERENCES `data_perpetualmotion`.`dates_dbtable` (`date_id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
