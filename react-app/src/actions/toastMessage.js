export function addMessage(msg) {
  return {
      type: 'ADD_MESSAGE',
      msg
  }
}
  
export function setMessageToRead() {
    
  return {
    type: 'SET_TO_READ',
  }
}

export function hardReset() {
    
  return {
    type: 'HARD_RESET',
  }
}