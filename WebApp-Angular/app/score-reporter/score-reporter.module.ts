import { NgModule }       from '@angular/core';
import { FormsModule }    from '@angular/forms';
import { CommonModule }   from '@angular/common';

import { ScoreReporterService }			from './score-reporter.service';

import { ScoreReporterHomeComponent }	from './score-reporter-home.component';
import { ScoreReporterFormComponent }	from './score-reporter-form.component';
import { ScoreReporterComponent }		from './score-reporter.component';
import { SportsDirective }				from '../resources/sports/sports.directive';
import { ResourcesModule }				from '../resources/resources.module';

import { ScoreReporterRoutingModule }	from './score-reporter-routing.module';

@NgModule({
	imports: [
		CommonModule,
		FormsModule,
		ScoreReporterRoutingModule,
		ResourcesModule
	],
	declarations: [
		ScoreReporterComponent,
		ScoreReporterHomeComponent,
		ScoreReporterFormComponent
	],
	providers: [
		ScoreReporterService
	]
})
export class ScoreReporterModule {}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/