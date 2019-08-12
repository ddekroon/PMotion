export default {
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
	}
};