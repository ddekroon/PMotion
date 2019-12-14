import Store from '../store/scoreSubmission'

const initialState = Store

export default function scoreSubmissionReducer (state = initialState, action) {
  switch (action.type) {
    case 'SCORE_SUBMISSION_UPDATE': {
      if (action.scoreSubmission) {
        return Object.assign({}, state, action.scoreSubmission)
      }
      return Object.assign({}, state)
    }
    case 'SCORE_SUBMISSION_RESET': {
      return Object.assign({}, initialState, {
        name: state.name,
        email: state.email
      })
    }
    case 'SCORE_SUBMISSION_RESET_MATCHES': {
      return {
        ...state,
        matches: initialState.matches
      }
    }
    case 'SCORE_SUBMISSION_SENDING_START': {
      return Object.assign({}, state, {
        submitting: true
      })
    }
    case 'SCORE_SUBMISSION_SENDING_SUCCESS': {
      return Object.assign({}, state, {
        submitting: false,
        submitted: true,
        name: state.name,
        email: state.email
      })
    }
    case 'SCORE_SUBMISSION_SENDING_ERROR': {
      return Object.assign({}, state, {
        submitting: false
      })
    }
    default:
      return Object.assign({}, state)
  }
}
