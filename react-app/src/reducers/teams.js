import Store from '../store/teams';

export const initialState = Store

function team(state = {}, action) {
	switch (action.type) {
		case 'RESET_TEAMS':
			return Object.assign({}, initialState);
		case 'INVALIDATE_TEAM':
			return Object.assign({}, state, {
				didInvalidate: true
			})
		case 'REQUEST_TEAM':
			return Object.assign({}, state, {
				isFetching: true,
				didInvalidate: false
			})
		case 'RECEIVE_TEAM':
			return Object.assign({}, state, {
				isFetching: false,
				didInvalidate: false,
				lastUpdated: action.receivedAt
			}, action.data);
		default:
			return state
	}
}

export default function teamsReducer(state = initialState, action) {
	switch (action.type) {
		case 'RESET_TEAMS':
		case 'INVALIDATE_TEAM':
		case 'REQUEST_TEAM':
		case 'RECEIVE_TEAM':
			return Object.assign({}, state, {
				[action.id]: team(state[action.id], action)
			})
		default:
			return state
	}
}