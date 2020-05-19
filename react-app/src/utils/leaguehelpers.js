import DateTimeHelpers from './datetimehelpers'

export default {
  getLeagueFromSeasons: (seasons, leagueId) => {
    if (seasons == null || leagueId == null || leagueId <= 0) {
      return null
    }

    return seasons
      .flatMap(curSeason => curSeason.leagues)
      .find(curLeague => curLeague.id === leagueId)
  },

  isValidLeagueId: leagueId => {
    return !isNaN(leagueId) && parseInt(leagueId, 10) > 0
  },

  getFormattedLeagueName: league => {
    if (league == null) {
      return ''
    }

    return league.name + ' ' + DateTimeHelpers.getDayString(league.dayNumber)
  },

  getDate: (league, dateId) => {
    if (league === null || dateId === '') {
      return null
    }

    return league.dates.find(curDate => curDate.id === dateId)
  },

  getNumInLeague: (league, teamId) => {
    if (league === null || teamId === '') {
      return ''
    }

    var team = league.teams.find(team => team.id === teamId)

    return team != null ? team.teamNumInLeague : ''
  },

  getTeamName: (league, teamId) => {
    if (league === null || teamId === '') {
      return ''
    }

    var team = league.teams.find(curTeam => curTeam.id === teamId)

    return team != null ? team.name : ''
  },

  convertMatchTime: time => {
    // This is just for the schedules bc theyre always at night
    // we can change it later..
    const newTime = (parseInt(time) - 1200).toString()
    return newTime.substr(0, 1) + ':' + newTime.substr(1, 2) + 'pm'
  },

  getMatchTimes: (league, venues, dateId) => {
    if (league === null || dateId === '') {
      return null
    }

    const times = {}
    var key = ''
    var prevKey = ''

    league.scheduledMatches.forEach(match => {
      if (match.dateId === dateId) {
        key = match.matchTime

        if (key != prevKey) {
          times[key] = {
            time: match.matchTime,
            matches: []
          }
        }
        prevKey = match.matchTime
      }
    })

    league.scheduledMatches.forEach(match => {
      if (match.dateId === dateId) {
        times[match.matchTime].matches.push({
          venue: venues[match.fieldId].name,
          team1: match.teamOneId,
          team2: match.teamTwoId,
          playoff1: match.playoffTeamOneString,
          playoff2: match.playoffTeamTwoString
        })
      }
    })

    Object.keys(times).forEach(time => {
      times[time].matches.sort((a, b) => {
        if (a.venue < b.venue) return -1
        if (a.venue > b.venue) return 1
        return 0
      })
    })

    return times
  },

  checkHideSpirit: (league) => {

    let curDay = new Date().getDay();
    let timeOfDay = new Date().getHours();
    let dayHide = parseInt(league.dayNumber);
    let dayShow = dayHide + parseInt(league.numDaysSpiritHidden);

    if(dayShow > 7){
        dayShow = dayShow % 7;
    }

    if(curDay == dayHide){
        return timeOfDay >= league.hideSpiritHour;
    }

    if(curDay == dayShow){
        return !(timeOfDay >= league.showSpiritHour);
    }

    return (curDay > dayHide && curDay < dayShow || curDay == 1 && dayHide == 7); 
  }
}
