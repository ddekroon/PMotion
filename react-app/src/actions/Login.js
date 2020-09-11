export function setLoggedIn(value) {
  return {
    type: 'SET_IS_LOGGEDIN',
    value
  }
}

export function getLoginInfo() {

  return {
    type: 'GET_LOGIN_INFO',
  }
}

export function logOut() {
  
  return {
    type: 'LOG_OUT',
  }
}

  