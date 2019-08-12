import Enums from '../constants/enums'

export const initialState = {
  type: Enums.messageTypes.None,
  message: ''
};

export default function appReducer(state = initialState, action) {
  switch (action.type) {
    case 'STATUS_REPLACE': {
      return {
        ...state,
        message: action.message || initialState
      };
    }
    default:
      return state;
  }
}
