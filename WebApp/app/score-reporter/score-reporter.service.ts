export class ScoreSubmission {
	public id: number;
	public leagueId: number;
	public teamId: number;
	//public teamId: number;
		
	constructor() { }
}

import { Injectable } from '@angular/core';

@Injectable()
export class ScoreReporterService {

	getScoreSubmission(id: number | string) {
		return Promise.resolve(new ScoreSubmission()); //Doesn't use the id at this point, could be useful later if we want to auto-save submissions or something
	}

	saveScoreSubmission(ss: ScoreSubmission) {
		//return Promise.resolve(ss)
		//	.then(ss => ss.find(ss => ss.id === +id));
		
		//TODO: need to create http call to save the current ScoreSubmission.
	}
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/