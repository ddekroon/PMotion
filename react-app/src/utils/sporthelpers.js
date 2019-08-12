const isValidSportId = (sportId) => {
	return sportId != null && !isNaN(sportId) && parseInt(sportId, 10) > 0;
}

const getSportById = (sports, sportId) => {

	if (sports == null || sports.length === 0 || !isValidSportId(sportId)) {
		return null;
	}

	return sports.find((curSport) => curSport.id == sportId);
}

export default {
	isValidSportId,
	getSportById
};