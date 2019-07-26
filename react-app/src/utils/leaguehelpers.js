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

		//return leagues.dates.filter((date) => {date.id === dateId});

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

		//return leagues.teams.filter((team) => {team.id === teamId});

		return teamNum;
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

		let times = {};
		var key = '';
		var prevKey = '';

		league.scheduledMatches.forEach((match) =>{
			if(match.dateId === dateId){
				key = match.matchTime;

				if(key != prevKey){
					times[key] = {
						time: match.matchTime,
						matches: [],
					};
				}
				prevKey = match.matchTime;
			}
		});

		league.scheduledMatches.forEach((match) => {
			if(match.dateId === dateId){
				times[match.matchTime].matches.push({
					venue: match.fieldId,
					team1: match.teamOneId,
					team2: match.teamTwoId,
				});
			}
		});

		
		Object.keys(times).forEach((time) => {
			times[time].matches.sort((a,b) => parseInt(a.venue) - parseInt(b.venue));
		});
		
		return times;
	},

};