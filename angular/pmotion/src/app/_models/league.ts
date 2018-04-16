import { Team } from './team';

export class League {
    name: string;
	seasonId: number;
	sportId: number;
    dayNumber: number;
	registrationFee: number;
	isAskForScores: boolean;
	numMatches: number;
	numGamesPerMatch: number;
	isTies: boolean;
	isPracticeGames: boolean;
	maxPointsPerGame: number;
	isShowCancelOption: boolean;
	isSendLateEmail: boolean;
	hideSpiritHour: string;
	showSpiritHour: string;
	numDaysSpiritHidden: number;
	weekInScoreReporter: number;
	weekInStandings: number;
	isSortByWinPct: boolean;
	isShowSpirit: boolean;
	isAllowIndividuals: boolean;
	isAvailableForRegistration: boolean;
	numTeamsBeforeWaiting: number;
	maximumTeams: number;
	playoffWeek: number;
	individualRegistrationFee: number;
	picLink: string;
	scheduleLink: string;
	isSplit: boolean;
	splitWeek: number;
	isFullIndividualMales: boolean;
	isFullIndividualFemales: boolean;
	isFullTeams: boolean;
	isShowStaticSchedule: boolean;
	
	dateInStandings: string;
	dateInScoreReporter: string;
	
	//season;
	//sport;
	teams: Team[];
	
}