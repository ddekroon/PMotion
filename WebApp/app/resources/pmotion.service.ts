import { Injectable }    from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';

@Injectable()
export class PMotionService {
	protected baseUrl = 'http://local.perpetualmotion.org/Derek/api';  // URL to web api
	protected headers = new Headers({'Content-Type': 'application/json'});

	constructor(protected http: Http) { }

	protected handleError(error: any): Promise<any> {
		console.error('An error occurred', error); // for demo purposes only
		return Promise.reject(error.message || error);
	}
}
