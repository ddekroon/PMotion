import { Component, OnInit } from '@angular/core';

import { User, Team } from '../_models/index';
import { UserService, TeamService } from '../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html'
})

export class HomeComponent implements OnInit {
    currentUser: User;
    teams: Team[] = [];

    constructor(
			private userService: UserService,
			private teamService: TeamService
		) {
        this.currentUser = JSON.parse(localStorage.getItem('currentUser'));
    }

    ngOnInit() {
        this.loadDashboardData();
    }

    /* deleteUser(id: number) {
        this.userService.delete(id).subscribe(() => { this.loadAllUsers() });
    } */

    private loadDashboardData() {
        this.teamService.getActiveTeamsForUser(this.currentUser.id).subscribe(teams => { 
			this.teams = teams; 
		});
    }
}