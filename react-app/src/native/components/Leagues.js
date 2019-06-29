import React from 'react';
import { createAppContainer, createMaterialTopTabNavigator, createStackNavigator } from 'react-navigation';
import Loading from './Loading'
import SportLeagueNav from '../components/SportLeagueNav';

const sportsNavigator = value => createMaterialTopTabNavigator(
  {
    Ultimate: props => <SportLeagueNav {...value} {...props} sportId='1' />,
    VolleyBall: props => <SportLeagueNav {...value} {...props} sportId='2' />,
    Soccer: props => <SportLeagueNav {...value} {...props} sportId='4' />,
    Football: props => <SportLeagueNav {...value} {...props} sportId='3' />
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
  constructor(props) {
    super(props);

    this.state = {
      isShowingText: true,
      sports: [],
      loading: true,
      seasonsWithLeaguesBySport: { 1: [], 2: [], 3: [], 4: [] }
    };
  }

  componentDidMount() {
    this.state.sports = this.props.lookups.sports;
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

    this.setState({
      seasonsWithLeaguesBySport: localSeasonsWithLeaguesBySport,
      loading: false
    })
  }

  render() {
    if (this.state.loading) return <Loading />;

    const SportsNavigator = createAppContainer(sportsNavigator(this.state));
    return (
      <SportsNavigator />
    )
  }
}

//this.setState({currentSport: this.props.lookups.sports[currentState.index].id});
//this wont work right in the way it is set up right now, i have to change it for tabs instead of stack nav changes

