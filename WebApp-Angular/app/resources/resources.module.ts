import { NgModule }       from '@angular/core';
import { FormsModule }    from '@angular/forms';
import { CommonModule }   from '@angular/common';

import { SportsService } from './sports/sports.service';

import { SportsDirective } from './sports/sports.directive';


@NgModule({
	imports: [
		CommonModule,
		FormsModule,
	],
	declarations: [
		SportsDirective
	],
	providers: [
		SportsService
	],
	exports: [
	]
})
export class ResourcesModule {}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/