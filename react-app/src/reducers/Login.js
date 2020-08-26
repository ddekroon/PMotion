import Store from '../store/Login';

export const initialState = Store

export default function waiverReducer(state = {}, action) {

	switch (action.type) {

		case 'SET_IS_LOGGEDIN':
			return Object.assign({}, state, {
				isLoggedIn: true,
				userName: action.value.userName,
				userFirstName :action.value.userFirstName,
				userLastName: action.value.userLastName,
				userId: action.value.userId,
				userAge: action.value.userAge,
			})

		case 'GET_LOGIN_INFO':
			var copy = Store
			console.log("Copy inside = " + JSON.stringify(copy))
			return copy

		case 'LOG_OUT':
			return Object.assign({}, state, {
				isLoggedIn:false,
				userName:'',
				userFirstName:'',
				userLastName:'',
				userId:'',
				userAge:'',
			})

		default:
			return state
	}
}

//isLoggedIn: action.value.isLoggedIn,