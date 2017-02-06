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
var score_reporter_home_component_1 = require('./score-reporter-home.component');
var score_reporter_form_component_1 = require('./score-reporter-form.component');
var score_reporter_component_1 = require('./score-reporter.component');
var can_deactivate_guard_service_1 = require('../can-deactivate-guard.service');
var score_reporter_resolver_service_1 = require('./score-reporter-resolver.service');
var scoreReporterRoutes = [
    {
        path: 'score-reporter',
        component: score_reporter_component_1.ScoreReporterComponent,
        children: [
            {
                path: ':sId',
                component: score_reporter_form_component_1.ScoreReporterFormComponent,
                canDeactivate: [can_deactivate_guard_service_1.CanDeactivateGuard],
                resolve: {
                    scoreSubmission: score_reporter_resolver_service_1.ScoreReporterResolver
                }
            },
            {
                path: '',
                component: score_reporter_home_component_1.ScoreReporterHomeComponent
            }
        ]
    }
];
var ScoreReporterRoutingModule = (function () {
    function ScoreReporterRoutingModule() {
    }
    ScoreReporterRoutingModule = __decorate([
        core_1.NgModule({
            imports: [
                router_1.RouterModule.forChild(scoreReporterRoutes)
            ],
            exports: [
                router_1.RouterModule
            ],
            providers: [
                score_reporter_resolver_service_1.ScoreReporterResolver
            ]
        }), 
        __metadata('design:paramtypes', [])
    ], ScoreReporterRoutingModule);
    return ScoreReporterRoutingModule;
}());
exports.ScoreReporterRoutingModule = ScoreReporterRoutingModule;
//# sourceMappingURL=score-reporter-routing.module.js.map