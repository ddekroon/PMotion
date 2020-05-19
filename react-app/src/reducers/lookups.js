import Store from '../store/lookup'

export const initialState = Store

export default function lookupReducer(state = initialState, action) {
  switch (action.type) {
    case 'SET_LOOKUPS': {
      if (action.data) {
        return {
          ...state,
          isFetching: false,
          seasonsAvailableRegistration:
            action.data.seasonsAvailableRegistration,
          seasonsAvailableScoreReporter:
            action.data.seasonsAvailableScoreReporter,
          sports: action.data.sports,
          scoreReporterSeasons: action.data.scoreReporterSeasons,
          registrationSeasons: action.data.registrationSeasons,
          venues: action.data.venues
        }
      }
      return initialState
    }
    case 'REQUEST_LOOKUPS': {
      return Object.assign({}, state, {
        isFetching: true
      })
    }
    case 'LOOKUP_RESET': {
      return initialState
    }
    default:
      return state
  }
}
