/**
 * Get lookups
 */
export function getLookups(dispatch) {
  return function(dispatch) {
    console.log('Get lookups from api and store them in the store')
    dispatch({
      type: 'REQUEST_LOOKUPS'
    })

    return fetch('http://data.perpetualmotion.org/web-app/api/lookups')
      .then(response => response.json())
      .then(responseJson => {
        let scoreReporterSeasons = parseSeasonsBySport(
          responseJson.sports,
          responseJson.seasonsAvailableScoreReporter
        )
        let registrationSeasons = parseSeasonsBySport(
          responseJson.sports,
          responseJson.seasonsAvailableRegistration
        )

        responseJson['scoreReporterSeasons'] = scoreReporterSeasons
        responseJson['registrationSeasons'] = registrationSeasons

        dispatch({
          type: 'SET_LOOKUPS',
          data: responseJson
        })
      })
      .catch(error => {
        console.error(error)
      })
  }
}

/**
 * Reset lookups
 */
export function resetLookups(dispatch) {
  return dispatch({
    type: 'RESET_LOOKUPS',
    data: []
  })
}

const parseSeasonsBySport = (sports, seasons) => {
  if (
    typeof sports === 'undefined' ||
    typeof seasons === 'undefined' ||
    sports == null ||
    seasons == null
  ) {
    return []
  }

  var localSeasonsWithLeaguesBySport = {}

  sports.forEach(function(curSport, index) {
    var seasonsForSport = []

    seasons.forEach(function(curSeason, index) {
      var season = {
        name: curSeason.name,
        year: curSeason.year,
        leagues: []
      }

      if (curSeason.leagues == null) {
        //season doesn't have any leagues yet
        seasonsForSport.push(season)
        return true
      }

      var curSeasonAndSportLeagues = curSeason.leagues.filter(
        league => league.sportId == curSport.id
      )
      curSeasonAndSportLeagues.sort(function compare(a, b) {
        if (a.dayNumber < b.dayNumber) {
          return -1
        }

        if (b.dayNumber < a.dayNumber) {
          return 1
        }

        return a.name < b.name ? -1 : 1
      })

      season.leagues = curSeasonAndSportLeagues
      seasonsForSport.push(season)
    })

    localSeasonsWithLeaguesBySport[curSport.id] = seasonsForSport
  })

  return localSeasonsWithLeaguesBySport
}
