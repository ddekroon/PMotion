import Store from '../store/lookup';

export const initialState = Store;

export default function lookupReducer(state = initialState, action) {
  switch (action.type) {
    case 'SET_LOOKUPS': {
      if (action.data) {
        return {
          ...state,
          loading: false,
          seasonsAvailableRegistration: action.data.seasonsAvailableRegistration,
          seasonsAvailableScoreReporter: action.data.seasonsAvailableScoreReporter,
          sports: action.data.sports
        };
      }
      return initialState;
    }
    case 'LOOKUP_RESET': {
      return initialState;
    }
    default:
      return state;
  }
}
