/**
 * Get lookups
 */


export function fetchTeam(teamId) {
  return (dispatch, getState) => {
    if (shouldFetchTeam(getState(), teamId)) {
      console.log('Get team from api and store it in the team store')
      dispatch({
        type: 'REQUEST_TEAM',
        id: teamId
      })

      return fetch(
        'http://data.perpetualmotion.org/web-app/api/teams/' + teamId
      )
        .then(
          response => response.json(),
          error => console.log('An error occurred.', error)
        )
        .then(json => {

          dispatch({
            type: 'RECEIVE_TEAM',
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

export function resetTeamStore() {
  return {
    type: 'RESET_TEAMS'
  };
}

function shouldFetchTeam(state, teamId) {
    
  /*
  if (!LeagueHelpers.isValidLeagueId(leagueId)) {
    return false
  }
  */

  const team = state.teams[teamId]
  if (!team) {
    return true
  } else if (team.isFetching) {
    return false
  } else {
    return team.didInvalidate
  }
}





