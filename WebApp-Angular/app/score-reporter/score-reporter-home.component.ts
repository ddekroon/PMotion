import { Component, OnInit } from '@angular/core';

import { Sport } from '../resources/sports/sport';

import { SportsService } from '../resources/sports/sports.service';

@Component({
	moduleId: module.id,
	templateUrl: './templates/score-reporter-home.html'
})
export class ScoreReporterHomeComponent implements OnInit {

	sports: Sport[] = [];

	constructor(private sportsService: SportsService) { }

	ngOnInit(): void {
		this.sportsService.getSports()
			.then(sports => this.sports = sports);
	}
	
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/