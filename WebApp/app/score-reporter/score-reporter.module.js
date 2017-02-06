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
var forms_1 = require('@angular/forms');
var common_1 = require('@angular/common');
var score_reporter_service_1 = require('./score-reporter.service');
var score_reporter_home_component_1 = require('./score-reporter-home.component');
var score_reporter_form_component_1 = require('./score-reporter-form.component');
var score_reporter_component_1 = require('./score-reporter.component');
var resources_module_1 = require('../resources/resources.module');
var score_reporter_routing_module_1 = require('./score-reporter-routing.module');
var ScoreReporterModule = (function () {
    function ScoreReporterModule() {
    }
    ScoreReporterModule = __decorate([
        core_1.NgModule({
            imports: [
                common_1.CommonModule,
                forms_1.FormsModule,
                score_reporter_routing_module_1.ScoreReporterRoutingModule,
                resources_module_1.ResourcesModule
            ],
            declarations: [
                score_reporter_component_1.ScoreReporterComponent,
                score_reporter_home_component_1.ScoreReporterHomeComponent,
                score_reporter_form_component_1.ScoreReporterFormComponent
            ],
            providers: [
                score_reporter_service_1.ScoreReporterService
            ]
        }), 
        __metadata('design:paramtypes', [])
    ], ScoreReporterModule);
    return ScoreReporterModule;
}());
exports.ScoreReporterModule = ScoreReporterModule;
/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/ 
//# sourceMappingURL=score-reporter.module.js.map