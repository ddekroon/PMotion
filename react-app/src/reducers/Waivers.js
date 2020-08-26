import Store from '../store/Waiver';

export const initialState = Store

export default function waiverReducer(state = {}, action) {

	switch (action.type) {

		case 'NEW_WAIVER':
			console.log("Second")
			return Object.assign({}, state, {
				Waiver: action.Waiver
			})

		default:
			return state
	}
}