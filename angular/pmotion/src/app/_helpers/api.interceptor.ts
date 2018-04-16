import {Injectable, isDevMode} from '@angular/core';
import {HttpEvent, HttpInterceptor, HttpHandler, HttpRequest} from '@angular/common/http';
import {Observable} from 'rxjs/Observable';

@Injectable()
export class ApiInterceptor implements HttpInterceptor {
	intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
		
		let baseUrl = '';
		if(isDevMode()) {
			baseUrl = 'http://local.perpetualmotion.org/web-app';
		} else {
			baseUrl = 'http://data.perpetualmotion.org/web-app';
		}
		
		let reqUrl = req.url;
		
		if(reqUrl.indexOf('/') != 0) {
			reqUrl = `/${reqUrl}`;
		}
		
		const apiReq = req.clone({ url: `${baseUrl}${reqUrl}` });
		return next.handle(apiReq);
	}
}