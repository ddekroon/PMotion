import { Injectable }    from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';

import { PMotionService } from '../pmotion.service'
import { Sport } from './sport';


@Injectable()
export class SportsService extends PMotionService {
	private sportsUrl = this.baseUrl + '/sports';  // URL to web api

	getSports() {
		return this.http.get(this.sportsUrl)
			.toPromise()
			.then(response => response.json().data as Sport[])
			.catch(this.handleError);
	}
	
	getSport(id: number | string) {
		const url = `${this.sportsUrl}/${id}`;
		return this.http.get(url)
			.toPromise()
			.then(response => response.json().data as Sport)
			.catch(this.handleError);
	}
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/