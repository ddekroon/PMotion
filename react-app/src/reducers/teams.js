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
		case 'NEW_TEAM':
			return Object.assign({}, state, {
				team:action.team
			});
		case 'RESTART':
			console.log("Resetting????")
			return Object.assign({}, state, {
				/*teamObj: undefined,
				team: undefined,
				help: undefined,
				league: undefined,
				paymentMethod: undefined,
				teamName: undefined,
				JSONarr: undefined,
				JSONarr: undefined,
				*/
				teamObj:undefined,
				title:undefined
			});
			
		default:
			return state
	}
}

/* Pretty sure I dont need this
function updateState(state = [], action) {
	console.log("HERE")
	switch(action.type) {
		case 'NEW_TEAM' :
			return [
				...state.slice(0,i), //b4 the one we are updating
				{...state[i], team: newTeam},
				...state.slice(i + 1),


			]
		default:
			return state
	}
} */