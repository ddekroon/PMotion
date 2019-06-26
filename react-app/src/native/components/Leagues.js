import React from 'react';
import { createAppContainer, createMaterialTopTabNavigator } from 'react-navigation';

import FootballScreen from '../components/Football';
import UltimateScreen from '../components/Ultimate';
import SoccerScreen from '../components/Soccer';
import VolleyballScreen from '../components/Volleyball';
import { Container } from 'native-base';

const sportsNavigator = value => createMaterialTopTabNavigator(
  {
    Ultimate: { screen: props => <UltimateScreen {...value} {...props} /> },
    VolleyBall: VolleyballScreen,
    Soccer: SoccerScreen,
    Football: FootballScreen,
  },

  {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: 'gray',
      style: {
        backgroundColor: '#303030'
      },
      indicatorStyle: {
        borderBottomColor: 'red',
        borderBottomWidth: 3,
      },
      labelStyle: {
        fontSize: 9
      },
    }
  },
);

//const AppIndex = createAppContainer(sportsNavigator)

export default class Leagues extends React.Component {
  //state object
  state = {
    isShowingText: true,
    currentSport: 1, //Ultimate
    seasonsWithLeaguesBySport: { 1: [], 2: [], 3: [], 4: [] }
  }

  componentDidMount() {

    var localSeasonsWithLeaguesBySport = {};
    var component = this;

    component.props.lookups.sports.forEach(function (curSport, index) {

      var seasonsForSport = [];

      component.props.lookups.seasonsAvailableScoreReporter.forEach(function (curSeason, index) {
        var season = {
          name: curSeason.name,
          year: curSeason.year,
          leagues: []
        }

        var curSeasonAndSportLeagues = curSeason.leagues.filter(league => league.sportId == curSport.id);
        curSeasonAndSportLeagues.sort(function compare(a, b) {
          if (a.dayNumber < b.dayNumber) {
            return -1;
          }

          if (b.dayNumber < a.dayNumber) {
            return 1;
          }

          return a.name < b.name ? -1 : 1;
        });

        season.leagues = curSeasonAndSportLeagues;
        seasonsForSport.push(season);
      });

      localSeasonsWithLeaguesBySport[curSport.id] = seasonsForSport;
    });

    component.state.seasonsWithLeaguesBySport = localSeasonsWithLeaguesBySport;
  }

  render() {
    const SportsNavigator = createAppContainer(sportsNavigator(this.state));
    return (
      <SportsNavigator
        onNavigationStateChange={(prevState, currentState) => {
          this.state.currentSport = this.props.lookups.sports[currentState.index].id;
        }}
      />
    )
  }
}
