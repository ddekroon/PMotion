import { Directive, OnInit, HostBinding }			from '@angular/core';
import { ActivatedRoute, Router, Params }			from '@angular/router';

import { Sport } from './sport';
import { SportsService } from './sports.service';

@Directive({
	selector: "sports"
})

export class SportsDirective implements OnInit {

	sports: Sport[];
	sportId: number;

	constructor(
		public sportsService: SportsService
	) {}

	ngOnInit() {
		
	}
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/