import ErrorMessages from '../constants/errors'
import ToastHelpers from '../utils/toasthelpers'
import Enums from '../constants/enums'

/**
 * Get lookups
 */
export function fetchTeam (teamId) {
  return (dispatch, getState) => {    
    if (teamId > 0 && shouldFetchTeam(getState(), teamId)) {
      dispatch({
        type: 'REQUEST_TEAM',
        id: teamId
      })

      return fetch(
        'https://data.perpetualmotion.org/web-app/api/teams/' + teamId
      )
        .then(
          response => response.json(),
        )
        .then(json => {
          console.log(json)
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
export function submitTeam(teamObj) {
  
  return (dispatch, getState) => new Promise(async (resolve, reject) => {
    /**
     * Url?: https://data.perpetualmotion.org/web-app/save-team/-1 
    */
    
    dispatch({
      type: 'SCORE_SUBMISSION_SENDING_START'
    })

    return fetch('https://data.perpetualmotion.org/web-app/dashboard/registration/register-team/1', {
      method:'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(getState().teamSubmission)
    })
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
  const team = state.teams[teamId]
  if (!team) {
    return true
  } else if (team.isFetching) {
    return false
  } else {
    return team.didInvalidate
  }
}

export function saveTeamToState(team) {
  return {
    type: 'SAVE_TEAM',
    team
  }
}

export function reset(){    //temp function I use when testing
  return {
    type:'RESTART'
  }
}