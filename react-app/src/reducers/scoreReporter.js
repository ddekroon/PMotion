import Store from '../store/scoreReporter';

export const initialState = Store;

export default function scoreReporterReducer(state = initialState, action) {
  switch (action.type) {
    case 'DETAILS_UPDATE': {
      if (action.data) {
        return {
          ...state,
          loading: false,
          error: null,
        };
      }
      return initialState;
    }
    case 'DETAILS_RESET': {
      return initialState;
    }
    default:
      return state;
  }
}
