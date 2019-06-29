import React from 'react';
import { createAppContainer, createMaterialTopTabNavigator, createStackNavigator } from 'react-navigation';

import SportLeagueNav from '../components/SportLeagueNav';
import LeagueOptionsNav from '../components/LeagueOptionsNav';

const divisionNavOptions = {
  navigationOptions: () => ({
    headerBackTitle: null,
    headerTintColor: 'red',
  }),
}

const homeNavOptions = {
  navigationOptions: () => ({
    header: null,
    headerBackTitle: null,
  })
}
   
export const sportStackNavigator = value => createStackNavigator ({
  //theres lots of optons we can style with here
    Home: {
      screen: sportsTabs(value),
      ...homeNavOptions
    },
    LeagueOptionsNav: {
      screen: props => <LeagueOptionsNav {...value} {...props} />,
      ...divisionNavOptions,
    },
  });

const sportsTabs = value => createMaterialTopTabNavigator(
  {
    Ultimate: props => <SportLeagueNav {...value} {...props} />,
    VolleyBall: props => <SportLeagueNav {...value} {...props} />,
    Soccer: props => <SportLeagueNav {...value} {...props} />,
    Football: props => <SportLeagueNav {...value} {...props} />,
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
    const SportsNavigator = createAppContainer(sportStackNavigator(this.state));
    return (
      <SportsNavigator
        onNavigationStateChange={(prevState, currentState) => {
          this.state.currentSport = this.props.lookups.sports[currentState.index].id;
        }}
      />
    )
  }
}

//this.setState({currentSport: this.props.lookups.sports[currentState.index].id});
//this wont work right in the way it is set up right now, i have to change it for tabs instead of stack nav changes

