import Store from '../store/Waiver';

export const initialState = Store

export default function waiverReducer(state = initialState, action) {
	console.log("state = " + JSON.stringify(state))
	switch (action.type) {

		case 'NEW_WAIVER':
			return Object.assign({}, state, {
				Waiver: action.Waiver
			})
			
		default:
			return state
	}
}