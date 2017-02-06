import { Injectable }             from '@angular/core';
import { Router, Resolve, RouterStateSnapshot,
         ActivatedRouteSnapshot } from '@angular/router';

import { ScoreSubmission, ScoreReporterService } from './score-reporter.service';

@Injectable()
export class ScoreReporterResolver implements Resolve<ScoreSubmission> {
  constructor(private srs: ScoreReporterService, private router: Router) {}

  resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<ScoreSubmission> {
    let id = route.params['id'];

    return this.srs.getScoreSubmission(id).then(ss => {
      if (ss) {
        return ss;
      } else { // id not found - this will never happen at this point.
        this.router.navigate(['/score-reporter']);
        return null;
      }
    });
  }
}


/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/