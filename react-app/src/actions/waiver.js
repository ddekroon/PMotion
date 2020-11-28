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

  return (dispatch, getState) => new Promise(async (resolve, reject) => {

    dispatch({
      type: 'WAIVER_SENDING_START',
    })

    return fetch('https://Data.perpetualmotion.org/web-app/API/waiver', { //Given endpoint from Derek: Data.perpetualmotion.org/web-app/API/waiver
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(obj)
    })
      //.then(NetworkHelpers.handleErrors)
      .then(
        (response) => response.json()
      )
      .then(json => {
        console.log("JSON = " + JSON.stringify(json))   //Returning a weird error message here
        dispatch({
          type: 'WAIVER_SENDING_SUCCESS',
          payload:json
        })
      })
      .catch((err) => {
        console.log("error = " + err)
        ToastHelpers.showToast(Enums.messageTypes.Error, ErrorMessages.errorSendingToServer)

        dispatch({
          type: 'WAIVER_SENDING_ERROR'
        })
      })
  })
}

export function saveWaiverToState(waiver) {
  return {
    type: 'NEW_WAIVER',
    waiver
  }
}