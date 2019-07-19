import DateTimeHelpers from './datetimehelpers'

export default {
	getLeagueFromSeasons: (seasons, leagueId) => {
		var league = null;

		if (seasons == null || leagueId == null || leagueId <= 0) {
			return league;
		}

		seasons.forEach((curSeason) => {
			if (league != null) {
				return false; //break
			}

			league = curSeason.leagues.find((curLeague) => curLeague.id == leagueId);
		})

		return league;
	},

	isValidLeagueId: (leagueId) => {
		return !isNaN(leagueId) && parseInt(leagueId, 10) > 0;
	},

	getFormattedLeagueName: (league) => {
		if (league == null) {
			return '';
		}

		return league.name + ' ' + DateTimeHelpers.getDayString(league.dayNumber);
	},

	getDate: (league, dateId) => {
		//dateId: league.scheduledMatches.dateId
		var getdate = {};

		if(league === null || dateId === ''){
			return null;
		}

		league.dates.forEach((date) => {
			if(date.id === dateId){
				getdate = date;
			}
		});

		return getdate;
	},

	getNumInLeague: (league, teamId) => {
		//teamId: league.scheduledMatches.teamOneId
		var teamNum = '';

		if(league === null || teamId === ''){
			return '';
		}

		league.teams.forEach((team) => {
			if(team.id === teamId){
				teamNum = team.numInLeague;
			}
		});

		return teamNum;
	},

	getVenueName: (lookups, fieldId) => {
		//fieldId: league.scheduledMatches.fieldId
		if(lookups === null || fieldId === ''){
			return '';
		}

		lookups.venues.forEach((venue) => {
			if(venue.id === fieldId){
				return venue.name;
			}
		});
		
	},

	convertMatchTime: (time) => {
		//This is just for the schedules bc theyre always at night
		//we can change it later..
		let newTime = (parseInt(time) - 1200).toString();
		return newTime.substr(0,1) +':' + newTime.substr(1,2) + 'pm';
	},

	getMatchTimes: (league, dateId) => {

		if(league === null || dateId === ''){
			return null;
		}

		let matchTimes = [];

		league.scheduledMatches.forEach((match) =>{
			if(match.dateId === dateId){
				matchTimes.push(match.matchTime);
			}
		});

		return matchTimes;

	},

	getMatches: (league, dateId) => {
		if(league == null || dateId == ''){
			return null;
		}

		let matches = [];

		league.scheduledMatches.forEach((match) =>{
			if(match.dateId === dateId){
				matches.push({
					venue: 'Location #',
					time: match.matchTime,
					team1: match.teamOneId,
					team2: match.teamTwoId,
				});
			}
		});

		return matches;
	},

};