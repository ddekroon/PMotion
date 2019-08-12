import Enums from '../constants/enums'

export default {
	isValidEmail: (email) => {
		let reg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		return reg.test(email);
	},

	isValidId: (id) => {
		return !isNaN(id) && parseInt(id, 10) > 0;
	},

	isValidGameResult: (resultValue) => {
		const validResults = Object.values(Enums.matchResult).filter((resultType) => resultType != Enums.matchResult.Error);

		return validResults.find((resultType) => resultType.val == resultValue) != null;
	}
}