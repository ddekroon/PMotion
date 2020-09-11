import ErrorMessages from '../constants/errors'
import ToastHelpers from '../utils/toasthelpers'
import Enums from '../constants/enums'

/**
 * Submit score to the server
 */
export function submitForgotPassword (obj) {

  return (dispatch, getState) => new Promise(async (resolve, reject) => {

    dispatch({
      type: 'FORGOT_PASSWORD_SENDING_START',
    })
    return fetch('https://data.perpetualmotion.org/web-app/request-reset-password', {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(obj)
    })
      //.then(NetworkHelpers.handleErrors)
      .then(
        (response) => response.json()  //This is just a test
      )
      .then(json => {
        dispatch({
          type: 'FORGOT_PASSWORD_SENDING_SUCCESS',
          payload:json
        })
      })
      .catch((err) => {
        console.log("error = " + err)
        ToastHelpers.showToast(Enums.messageTypes.Error, ErrorMessages.errorSendingToServer)

        dispatch({
          type: 'FORGOT_PASSWORD_SENDING_ERROR'
        })
      })
  })
}