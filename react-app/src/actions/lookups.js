/**
  * Get lookups
  */
export function getLookups(dispatch) {
  console.log("Get lookups from api and store them in the store");

  return function(dispatch) {
    return fetch('http://data.perpetualmotion.org/web-app/api/lookups')
      .then((response) => response.json())
      .then((responseJson) => {
        dispatch({
          type: 'SET_LOOKUPS',
          data: responseJson
        });
      })
      .catch((error) => {
        console.error(error);
      });
  }
}

/**
  * Reset lookups
  */
export function resetLookups(dispatch) {
  return dispatch({
    type: 'RESET_LOOKUPS',
    data: [],
  });
}