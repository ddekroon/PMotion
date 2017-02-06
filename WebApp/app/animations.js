"use strict";
var core_1 = require('@angular/core');
// Component transition animations
exports.slideInDownAnimation = core_1.trigger('routeAnimation', [
    core_1.state('*', core_1.style({
        opacity: 1,
        transform: 'translateX(0)'
    })),
    core_1.transition(':enter', [
        core_1.style({
            opacity: 0,
            transform: 'translateX(-100%)'
        }),
        core_1.animate('0.2s ease-in')
    ]),
    core_1.transition(':leave', [
        core_1.animate('0.5s ease-out', core_1.style({
            opacity: 0,
            transform: 'translateY(100%)'
        }))
    ])
]);
/*
Copyright 2016 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/ 
//# sourceMappingURL=animations.js.map