import ErrorMessages from '../constants/errors'
import ToastHelpers from '../utils/toasthelpers'
import Enums from '../constants/enums'
import NetworkHelpers from '../utils/networkhelpers'
import ValidationHelpers from '../utils/validationhelpers'

/**
 * Get lookups
 */
export function fetchTeam (teamId) {
  return (dispatch, getState) => {
    if (shouldFetchTeam(getState(), teamId)) {
      console.log('Get team from api and store it in the team store')
      dispatch({
        type: 'REQUEST_TEAM',
        id: teamId
      })

      return fetch(
        'https://data.perpetualmotion.org/web-app/api/teams/' + teamId
      )
        .then(
          response => response.json(),
          error => console.log('An error occurred.', error)
        )
        .then(json => {
          console.log("JSON(src/actions/team.js) = " + JSON.stringify(JSON))
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

export function resetTeamStore () {
  return {
    type: 'RESET_TEAMS'
  }
}

//Pushing the created team to the server
export function submitTeam() {
  console.log("/actions/teams.submitTeam")
  return (dispatch, getState) => new Promise(async (resolve, reject) => {
    dispatch({
      type: 'SCORE_SUBMISSION_SENDING_START'
    })

    console.log('Submitting new team')

    return fetch('https://data.perpetualmotion.org/web-app/api/teams/', {
      method:'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(getState().teamSubmission)
    })
      .then(NetworkHelpers.handleErrors) //this throws an arror every time and IDK why
      .then(
        response => response.json()
      )
      .then(json => {
        dispatch({
          type: 'TEAM_SUBMISSION_SENDING_SUCCESS'
        })
      })
      .catch((err) => {
        console.log(err)
        ToastHelpers.showToast(Enums.messageTypes.Error, ErrorMessages.errorSendingToServer)

        dispatch({
          type: 'TEAM_SUBMISSION_SENDING_ERROR'
        })
      })
  })
}

function shouldFetchTeam (state, teamId) {
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
