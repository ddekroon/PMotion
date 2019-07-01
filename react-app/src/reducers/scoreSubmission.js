import Store from '../store/scoreSubmission';

export const initialState = Store;

export default function scoreSubmissionReducer(state = initialState, action) {
  switch (action.type) {
    case 'SCORE_SUBMISSION_UPDATE': {
      if (action.scoreSubmission) {
        return Object.assign({}, state, action.scoreSubmission);
      }
      return initialState;
    }
    case 'SCORE_SUBMISSION_RESET': {
      return initialState;
    }
    default:
      return state;
  }
}
