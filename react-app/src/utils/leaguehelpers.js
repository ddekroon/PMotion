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
	}
};