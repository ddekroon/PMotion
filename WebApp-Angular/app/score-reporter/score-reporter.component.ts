import { Component, OnInit, HostBinding }			from '@angular/core';
import { ActivatedRoute, Router, Params }			from '@angular/router';

import { slideInDownAnimation }						from '../animations';
import { ScoreSubmission, ScoreReporterService }	from './score-reporter.service';
import { DialogService }							from '../dialog.service';

@Component({
	moduleId: module.id,
	selector: 'score-reporter',
	template: `
		<h2>Score Reporter</h2>
		<router-outlet></router-outlet>
	`,
	animations: [ slideInDownAnimation ],
})
export class ScoreReporterComponent implements OnInit {
	@HostBinding('@routeAnimation') routeAnimation = true;
	@HostBinding('style.display')   display = 'block';
	@HostBinding('style.position')  position = 'absolute';

	scoreSubmission: ScoreSubmission;
	sportId: number;

	constructor(
		private route: ActivatedRoute,
		private router: Router,
		public dialogService: DialogService,
		public scoreReporterService: ScoreReporterService
	) {}

	ngOnInit() {
		this.route.params
			.switchMap((params: Params) => {
				this.sportId = +params['sId'];
				this.scoreSubmission.leagueId = +params['lId'];
				this.scoreSubmission.teamId = +params['tId'];
				return Promise.resolve(this.scoreSubmission);
			});
	}

	cancel() {
		this.goHome();
	}

	save() {
		this.scoreReporterService.saveScoreSubmission(this.scoreSubmission);
		this.goHome();
	}

	canDeactivate(): Promise<boolean> | boolean {
		// Allow synchronous navigation (`true`) if no crisis or the crisis is unchanged
		//if (!this.crisis || this.crisis.name === this.editName) {
			return true;
		//}
		// Otherwise ask the user with the dialog service and return its
		// promise which resolves to true or false when the user decides
		//return this.dialogService.confirm('Discard changes?');
	}

	goHome() {
		this.router.navigate(['../'], { relativeTo: this.route });
	}
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/