import { League } from './league';

export class Team {
    leagueId: number;
    name: string;
    numInLeague: number;
    managedByUserId: number;
    wins: number;
    losses: number;
    ties: number;
    mostRecentWeekSubmitted: number;
    dateCreated: string;
    isFinalized: boolean;
    isPaid: boolean;
    isDeleted: boolean;
    paymentMethod: number;
    finalPosition: number;
    finalSpiritPosition: number;
    picName: string;
    isConvenor: boolean;
    isDroppedOut: boolean;
	isLateEmailAllowed: boolean;
	
	league: League;
}