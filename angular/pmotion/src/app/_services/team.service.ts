import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { Team } from '../_models/index';

@Injectable()
export class TeamService {
	
    constructor(private http: HttpClient) { }

    getActiveTeamsForUser(userID: number) {
        return this.http.get<Team[]>('/api/teams/active-teams-for-user/' + userID);
    }

    /* getById(id: number) {
        return this.http.get('/api/teams/' + id);
    }

    create(team: Team) {
        return this.http.post('/api/teams', team);
    }

    update(team: Team) {
        return this.http.put('/api/teams/' + team.id, team);
    }

    delete(id: number) {
        return this.http.delete('/api/teams/' + id);
    } */
}