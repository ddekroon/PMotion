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
var animations_1 = require('../animations');
var score_reporter_service_1 = require('./score-reporter.service');
var dialog_service_1 = require('../dialog.service');
var ScoreReporterComponent = (function () {
    function ScoreReporterComponent(route, router, dialogService, scoreReporterService) {
        this.route = route;
        this.router = router;
        this.dialogService = dialogService;
        this.scoreReporterService = scoreReporterService;
        this.routeAnimation = true;
        this.display = 'block';
        this.position = 'absolute';
    }
    ScoreReporterComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.route.params
            .switchMap(function (params) {
            _this.sportId = +params['sId'];
            _this.scoreSubmission.leagueId = +params['lId'];
            _this.scoreSubmission.teamId = +params['tId'];
            return Promise.resolve(_this.scoreSubmission);
        });
    };
    ScoreReporterComponent.prototype.cancel = function () {
        this.goHome();
    };
    ScoreReporterComponent.prototype.save = function () {
        this.scoreReporterService.saveScoreSubmission(this.scoreSubmission);
        this.goHome();
    };
    ScoreReporterComponent.prototype.canDeactivate = function () {
        // Allow synchronous navigation (`true`) if no crisis or the crisis is unchanged
        //if (!this.crisis || this.crisis.name === this.editName) {
        return true;
        //}
        // Otherwise ask the user with the dialog service and return its
        // promise which resolves to true or false when the user decides
        //return this.dialogService.confirm('Discard changes?');
    };
    ScoreReporterComponent.prototype.goHome = function () {
        this.router.navigate(['../'], { relativeTo: this.route });
    };
    __decorate([
        core_1.HostBinding('@routeAnimation'), 
        __metadata('design:type', Object)
    ], ScoreReporterComponent.prototype, "routeAnimation", void 0);
    __decorate([
        core_1.HostBinding('style.display'), 
        __metadata('design:type', Object)
    ], ScoreReporterComponent.prototype, "display", void 0);
    __decorate([
        core_1.HostBinding('style.position'), 
        __metadata('design:type', Object)
    ], ScoreReporterComponent.prototype, "position", void 0);
    ScoreReporterComponent = __decorate([
        core_1.Component({
            moduleId: module.id,
            selector: 'score-reporter',
            template: "\n\t\t<h2>Score Reporter</h2>\n\t\t<router-outlet></router-outlet>\n\t",
            animations: [animations_1.slideInDownAnimation],
        }), 
        __metadata('design:paramtypes', [router_1.ActivatedRoute, router_1.Router, dialog_service_1.DialogService, score_reporter_service_1.ScoreReporterService])
    ], ScoreReporterComponent);
    return ScoreReporterComponent;
}());
exports.ScoreReporterComponent = ScoreReporterComponent;
/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/ 
//# sourceMappingURL=score-reporter.component.js.map