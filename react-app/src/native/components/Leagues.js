import React from 'react';
import { createAppContainer, createMaterialTopTabNavigator, createStackNavigator } from 'react-navigation';

import FootballScreen from './sportTabs/Football';

import SoccerScreen from './sportTabs/Soccer';

import VolleyballScreen from './sportTabs/Volleyball';

import UltimateScreen from './sportTabs/Ultimate';
import B7Division from './ultimateLeagues/B7Division';
import BDivision from './ultimateLeagues/BDivision';
import CDivision from './ultimateLeagues/CDivision';
import BCDivision from './ultimateLeagues/BCDivision';
import C1B2Division from './ultimateLeagues/C1B2Division';
import C2Division from './ultimateLeagues/C2Division';

const divisionNavOptions = {
  navigationOptions: () => ({
    headerBackTitle: null,
    headerTintColor: 'red',
  }),
}

export const UltimateStack = value => createStackNavigator ({
  //theres lots of optons we can style with here
    Home: {
      screen: props => <UltimateScreen {...value} {...props} />,
      navigationOptions: () => ({
        header: null,
        headerBackTitle: null,
      }),
    },
    B7Division: {
      screen: B7Division,
      ...divisionNavOptions,
    },
    BDivision: {
      screen: BDivision,
      ...divisionNavOptions,
    },
    CDivision: {
      screen: CDivision,
      ...divisionNavOptions,
    },
    BCDivision: {
      screen: BCDivision,
      ...divisionNavOptions,
    },
    C1B2Division: {
      screen: C1B2Division,
      ...divisionNavOptions,
    },
    C2Division: {
      screen: C2Division,
      ...divisionNavOptions,
    }

  });

export const VolleyballStack = createStackNavigator({
    Home: VolleyballScreen,
  });

export const FootballStack = createStackNavigator({
  Home: FootballScreen,
});

export const SoccerStack = createStackNavigator({
  Home: SoccerScreen,
});

const sportsNavigator = value => createMaterialTopTabNavigator(
  {
    Ultimate: UltimateStack(value),
    VolleyBall: VolleyballStack,
    Soccer: SoccerStack,
    Football: FootballStack,
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
