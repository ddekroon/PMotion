import LeagueHelpers from '../utils/leaguehelpers'

/**
 * Get lookups
 */
export function fetchLeague (leagueId) {
  return (dispatch, getState) => {
    if (shouldFetchLeague(getState(), leagueId)) {
      console.log('Get league from api and store it in the leagues store')
      dispatch({
        type: 'REQUEST_LEAGUE',
        id: leagueId
      })

      return fetch(
        'https://data.perpetualmotion.org/web-app/api/leagues/' + leagueId
      )
        .then(
          response => response.json(),
          error => console.log('An error occurred.', error)
        )
        .then(json => {
          json['leagueSchedule'] = buildLeagueSchedule(
            json,
            getState().lookups.venues
          )

          dispatch({
            type: 'RECEIVE_LEAGUE',
            data: json,
            id: json.id,
            receivedAt: Date.now()
          })
        })
        .catch(error => {
          console.error(error)
        })
    } else {
      // Let the calling code know there's nothing to wait for.
      return Promise.resolve()
    }
  }
}

function shouldFetchLeague (state, leagueId) {
  if (!LeagueHelpers.isValidLeagueId(leagueId)) {
    return false
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

const buildLeagueSchedule = (league, venues) => {
  if (
    typeof league === 'undefined' ||
    league == null ||
    league.scheduledMatches === undefined ||
    league.scheduledMatches.length == 0
  ) {
    return []
  }

  return league.dates
    .sort((dateOne, dateTwo) => dateOne.weekNumber - dateTwo.weekNumber)
    .map(curDate => {
      return {
        date: curDate,
        times: LeagueHelpers.getMatchTimes(league, venues, curDate.id)
      }
    })
}
