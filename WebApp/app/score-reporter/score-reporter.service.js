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
var ScoreSubmission = (function () {
    //public teamId: number;
    function ScoreSubmission() {
    }
    return ScoreSubmission;
}());
exports.ScoreSubmission = ScoreSubmission;
var core_1 = require('@angular/core');
var ScoreReporterService = (function () {
    function ScoreReporterService() {
    }
    ScoreReporterService.prototype.getScoreSubmission = function (id) {
        return Promise.resolve(new ScoreSubmission()); //Doesn't use the id at this point, could be useful later if we want to auto-save submissions or something
    };
    ScoreReporterService.prototype.saveScoreSubmission = function (ss) {
        //return Promise.resolve(ss)
        //	.then(ss => ss.find(ss => ss.id === +id));
        //TODO: need to create http call to save the current ScoreSubmission.
    };
    ScoreReporterService = __decorate([
        core_1.Injectable(), 
        __metadata('design:paramtypes', [])
    ], ScoreReporterService);
    return ScoreReporterService;
}());
exports.ScoreReporterService = ScoreReporterService;
/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/ 
//# sourceMappingURL=score-reporter.service.js.map