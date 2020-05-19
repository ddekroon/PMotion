import Store from '../store/leagues';

export const initialState = Store

function league(state = {}, action) {
	switch (action.type) {
		case 'INVALIDATE_LEAGUE':
			return Object.assign({}, state, {
				didInvalidate: true
			})
		case 'REQUEST_LEAGUE':
			return Object.assign({}, state, {
				isFetching: true,
				didInvalidate: false
			})
		case 'RECEIVE_LEAGUE':
			return Object.assign({}, state, {
				isFetching: false,
				didInvalidate: false,
				lastUpdated: action.receivedAt
			}, action.data);
		default:
			return state
	}
}

export default function leaguesReducer(state = initialState, action) {
	switch (action.type) {
		case 'INVALIDATE_LEAGUE':
		case 'REQUEST_LEAGUE':
		case 'RECEIVE_LEAGUE':
			return Object.assign({}, state, {
				[action.id]: league(state[action.id], action)
			})
		default:
			return state
	}
}