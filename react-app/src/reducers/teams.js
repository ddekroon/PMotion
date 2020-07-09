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
			let variable = '' + action.team.id
			console.log("action id = " + action.team.id)
			return Object.assign({}, state, {
				[variable]:action.team
			});
		case 'RESTART':
			//been using this to delete test pushes
			return Object.assign({}, state, {
				4695:undefined,
				3933:undefined,
			});
			
		default:
			return state
	}
}

/* Pretty sure we dont need this
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