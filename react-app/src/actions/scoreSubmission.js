import ErrorMessages from '../constants/errors'
import ToastHelpers from '../utils/toasthelpers'
import Enums from '../constants/enums'
import NetworkHelpers from '../utils/networkhelpers'
import ValidationHelpers from '../utils/validationhelpers'

export function updateScoreSubmission (newScoreSubmission) {
  return {
    type: 'SCORE_SUBMISSION_UPDATE',
    scoreSubmission: newScoreSubmission
  }
}

export function resetMatches () {
  return {
    type: 'SCORE_SUBMISSION_RESET_MATCHES'
  }
}

export function resetSubmission () {
  return {
    type: 'SCORE_SUBMISSION_RESET'
  }
}

/*
 * Submit score to the server
 */
export function submitScoreSubmission () {

  return (dispatch, getState) => new Promise(async (resolve, reject) => {
    
    //Validation checks
    if (!isScoreSubmissionValid(getState().scoreSubmission, getState().leagues)) {
      return reject({ message: 'Your score submission has errors' })
    }

    dispatch({
      type: 'SCORE_SUBMISSION_SENDING_START'
    })

    return fetch('https://data.perpetualmotion.org/web-app/api/score-submission', {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(getState().scoreSubmission)
    })
      .then(
        response => response.json()
      )
      .then(json => {
        dispatch({
          type: 'SCORE_SUBMISSION_SENDING_SUCCESS'
        })
      })
      .catch((err) => {
        console.log(err)
        ToastHelpers.showToast(Enums.messageTypes.Error, ErrorMessages.errorSendingToServer)

        dispatch({
          type: 'SCORE_SUBMISSION_SENDING_ERROR'
        })
      })
    })
}

function isScoreSubmissionValid (scoreSubmission, leagues) {
  if (!ValidationHelpers.isValidId(scoreSubmission.sportId) ||
    !ValidationHelpers.isValidId(scoreSubmission.leagueId) ||
    !ValidationHelpers.isValidId(scoreSubmission.teamId)
  ) {
    return false
  }

  // Matches and scores are validated differently based on which league we're submitting for
  var league = leagues[scoreSubmission.leagueId]

  for (var i = 0; i < parseInt(league.numMatches, 10); i++) {
    const curMatch = scoreSubmission.matches[i]

    if (!ValidationHelpers.isValidId(curMatch.oppTeamId) ||
      !ValidationHelpers.isValidId(curMatch.spiritScore) ||
      parseFloat(curMatch.spiritScore) < 4 && curMatch.comment.length < 4
    ) {
      return false
    }

    for (var gameNum = 0; gameNum < parseInt(league.numGamesPerMatch, 10); gameNum++) {
      const curGame = curMatch.results[gameNum]

      if (!ValidationHelpers.isValidGameResult(curGame.result)) {
        return false
      }
    }
  }

  if (scoreSubmission.name.length < 3 ||
    !ValidationHelpers.isValidEmail(scoreSubmission.email)
  ) {
    return false
  }

  return true
}
