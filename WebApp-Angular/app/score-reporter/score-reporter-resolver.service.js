"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var core_1 = require('@angular/core');
var router_1 = require('@angular/router');
var score_reporter_service_1 = require('./score-reporter.service');
var ScoreReporterResolver = (function () {
    function ScoreReporterResolver(srs, router) {
        this.srs = srs;
        this.router = router;
    }
    ScoreReporterResolver.prototype.resolve = function (route, state) {
        var _this = this;
        var id = route.params['id'];
        return this.srs.getScoreSubmission(id).then(function (ss) {
            if (ss) {
                return ss;
            }
            else {
                _this.router.navigate(['/score-reporter']);
                return null;
            }
        });
    };
    ScoreReporterResolver = __decorate([
        core_1.Injectable(), 
        __metadata('design:paramtypes', [score_reporter_service_1.ScoreReporterService, router_1.Router])
    ], ScoreReporterResolver);
    return ScoreReporterResolver;
}());
exports.ScoreReporterResolver = ScoreReporterResolver;
/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/ 
//# sourceMappingURL=score-reporter-resolver.service.js.map