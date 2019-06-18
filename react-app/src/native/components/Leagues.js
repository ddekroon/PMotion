import React from 'react';
import { createAppContainer, createMaterialTopTabNavigator } from 'react-navigation';

import FootballScreen from '../components/Football';
import UltimateScreen from '../components/Ultimate';
import SoccerScreen from '../components/Soccer';
import VolleyballScreen from '../components/Volleyball';
import { Container } from 'native-base';

const appNavigator = createMaterialTopTabNavigator(
  {
    Ultimate: UltimateScreen,
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

const AppIndex = createAppContainer(appNavigator)

export default class Leagues extends React.Component {
  componentDidMount() {

    var leaguesBySportAndSeason = [];
    var component = this;

    console.log("Props in mount function of leagues component");
    //console.log(this.props.lookups.seasonsAvailableScoreReporter);
    //console.log("Hi derek");

    component.props.lookups.sports.forEach(function (curSport, index) {
      var seasonsForSport = [];

      component.props.lookups.seasonsAvailableScoreReporter.forEach(function (curSeason, index) {
        var season = {
          name: curSeason.name,
          year: curSeason.year,
          leagues: []
        }

        const curSeasonLeaguesBySport = curSeason.leagues.filter(league => league.sportId == curSport.id);
        curSeasonLeaguesBySport.sort(function compare(a, b) {
          if (a.dayNumber < b.dayNumber) {
            return -1;
          }

          if (b.dayNumber < a.dayNumber) {
            return 1;
          }

          return a.name < b.name ? -1 : 1;
        });

        season.leagues.push(curSeasonLeaguesBySport);
        seasonsForSport.push(season);
      });

      leaguesBySportAndSeason.push(seasonsForSport);
    });

    component.setState(previousState => (
      { leaguesBySport: leaguesBySportAndSeason }
    ))
  }

  //state object
  state = {
    isShowingText: true,
    currentSport: 0, //Ultimate
    leaguesBySport: []
  }

  render() {
    return (
      <AppIndex
        screenProps={{ data: { seasons: this.state.leaguesBySport[this.state.currentSport], label: "Hi Derek" } }}
        onNavigationStateChange={(prevState, currentState) => {
          this.state.currentSport = currentState.index;
        }}
      />
    )
  }
}
