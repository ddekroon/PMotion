import LeagueHelpers from '../utils/leaguehelpers'

/**
  * Get lookups
  */
export function fetchLeague(leagueId) {
  return (dispatch, getState) => {

    if (shouldFetchLeague(getState(), leagueId)) {
      console.log("Get league from api and store it in the leagues store");
      dispatch({
        type: 'REQUEST_LEAGUE',
        id: leagueId
      })

      return fetch('http://data.perpetualmotion.org/web-app/api/leagues/' + leagueId)
        .then(
          response => response.json(),
          error => console.log('An error occurred.', error)
        )
        .then(json => {

          let leagueSchedule = buildLeagueSchedule(json);
          json['leagueSchedule'] = leagueSchedule;

          dispatch({
            type: 'RECEIVE_LEAGUE',
            data: json,
            id: json.id,
            receivedAt: Date.now()
          })
        })
        .catch((error) => {
          console.error(error);
        });
    } else {
      // Let the calling code know there's nothing to wait for.
      return Promise.resolve()
    }
  }
}

function shouldFetchLeague(state, leagueId) {
  console.log("Should fetch league: " + leagueId);

  if (!LeagueHelpers.isValidLeagueId(leagueId)) {
    return false;
  }

  const league = state.leagues[leagueId]
  if (!league) {
    return true
  } else if (league.isFetching) {
    return false
  } else {
    return league.didInvalidate
  }
}

const buildLeagueSchedule = (league) => {

  if(typeof(league) === 'undefined' || league == null){
    return [];
  }

  const scheduleWeeks = [];
  let prevWeek = '';

  league.scheduledMatches.forEach((match, i) => {

    let date = LeagueHelpers.getDate(league, match.dateId);

    if(prevWeek != date.weekNumber){

      let matchTimes = LeagueHelpers.getMatchTimes(league, match.dateId);
      let matches = LeagueHelpers.getMatches(league, match.dateId);

      let week = {
          week: date.weekNumber,
          date: date,
          matchTimes: matchTimes,
          matches: matches
      };

      prevWeek = date.weekNumber;
      scheduleWeeks.push(week);
  }

  });

  return scheduleWeeks;
}


