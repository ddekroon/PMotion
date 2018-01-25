import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { ScoreReporterHomeComponent } from './score-reporter-home.component';
import { ScoreReporterFormComponent } from './score-reporter-form.component';
import { ScoreReporterComponent }     from './score-reporter.component';

import { CanDeactivateGuard }     from '../can-deactivate-guard.service';
import { ScoreReporterResolver }   from './score-reporter-resolver.service';

const scoreReporterRoutes: Routes = [
	{
		path: 'score-reporter',
		component: ScoreReporterComponent,
		children: [
			{
				path: ':sId',
				component: ScoreReporterFormComponent,
				canDeactivate: [CanDeactivateGuard],
				resolve: {
					scoreSubmission: ScoreReporterResolver
				}
			},
			{
			  path: '',
			  component: ScoreReporterHomeComponent
			}
		]
	}
];

@NgModule({
	imports: [
		RouterModule.forChild(scoreReporterRoutes)
	],
	exports: [
		RouterModule
	],
	providers: [
		ScoreReporterResolver
	]
})

export class ScoreReporterRoutingModule { }
