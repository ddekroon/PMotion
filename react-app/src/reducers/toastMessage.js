import Store from '../store/toastMessage';

export const initialState = Store

export default function toastMessageReducer(state = initialState, action) {
    
    switch (action.type) {
		case 'ADD_MESSAGE':
            return Object.assign({}, state, {
                message:action.msg,
                toBePrinted:true
            });
            
		case 'SET_TO_READ':
            return Object.assign({}, state, {
                toBePrinted:false
            });

        case 'HARD_RESET':
            const { routing } = state
            state = { routing }

            return state
			
		default:
			return state 
	}
}
