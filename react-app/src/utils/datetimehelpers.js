export default {		//Pass in a number and get the day back
	getDayString: (dayNumber) => {
		switch (parseInt(dayNumber, 10)) {
			case 1:
				return 'Monday';
			case 2:
				return 'Tuesday';
			case 3:
				return 'Wednesday';
			case 4:
				return 'Thursday';
			case 5:
				return 'Friday';
			case 6:
				return 'Saturday';
			default:
				return 'Sunday';
		}
	},
	getShortDate: (dateString) => {
		[ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ].forEach((date) => dateString = dateString.replace(date, ''));
		dateString = dateString.replace('January', 'Jan');
		dateString = dateString.replace('February', 'Feb');
		dateString = dateString.replace('March', 'Mar');
		dateString = dateString.replace('April', 'Apr');
		dateString = dateString.replace('August', 'Aug');
		dateString = dateString.replace('September', 'Sept');
		dateString = dateString.replace('October', 'Oct');
		dateString = dateString.replace('November', 'Nov');
		dateString = dateString.replace('December', 'Dec');
		dateString = dateString.replace(',', '');
		
		return dateString.trim();
	}
};

//export {
//	getDayFromString: (string) => {	//Pass in a string that contains the day, and then get the day back
export function getDayFromString(string) {
		if (string.includes('monday')) {
			return 'Monday'
		
		} else if (string.includes('tuesday')) {
			return 'Tuesday'
		
		} else if (string.includes('wednesday')) {
			return 'Wednesday'
		
		} else if (string.includes('thursday')) {
			return 'Thursday'
		
		} else if (string.includes('friday')) {
			return 'Friday'
		
		} else if (string.includes('saturday')) {
			return 'Saturday'
		
		} else if (string.includes('sunday')) {
			return 'Sunday'
		
		} else {
			return ""
		}
}

