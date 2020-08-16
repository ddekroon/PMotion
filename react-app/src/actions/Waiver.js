//Converting this to submit waivers
import ErrorMessages from '../constants/errors'
import ToastHelpers from '../utils/toasthelpers'
import Enums from '../constants/enums'
import NetworkHelpers from '../utils/networkhelpers'
import ValidationHelpers from '../utils/validationhelpers'

/**
 * Submit score to the server
 */
export function submitWaiver (obj) {
  console.log("actions/Waiver")
  return (dispatch, getState) => new Promise(async (resolve, reject) => {
    console.log("What will be submitted: " + JSON.stringify(getState()))

    dispatch({
      type: 'WAIVER_SENDING_START'
    })
    return fetch('https://data.perpetualmotion.org/waiver.php?sportID=1', {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(getState())
    })
      //.then(NetworkHelpers.handleErrors)
      .then(
        response => response.json()
      )
      .then(json => {
        dispatch({
          type: 'WAIVER_SENDING_SUCCESS'
        })
      })
      .catch((err) => {
        console.log(err)
        ToastHelpers.showToast(Enums.messageTypes.Error, ErrorMessages.errorSendingToServer)

        dispatch({
          type: 'WAIVER_SENDING_ERROR'
        })
      })
  })
}

export function saveWaiverToState(Waiver) {
  console.log("First")
  return {
    type: 'NEW_WAIVER',
    Waiver
  }
}