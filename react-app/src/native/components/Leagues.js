import React from 'react';
import PropTypes from 'prop-types';
import { createAppContainer, createMaterialTopTabNavigator, createStackNavigator } from 'react-navigation';
import Loading from './Loading'
import SportLeagueNav from '../components/SportLeagueNav';
import { Text } from 'native-base';

const sportsNavigator = (sports, seasonsBySport) => createMaterialTopTabNavigator(
  {
    Ultimate: props => <SportLeagueNav
      sports={sports}
      seasons={seasonsBySport['1']}
      sportId='1'
    />,
    VolleyBall: props => <SportLeagueNav
      sports={sports}
      seasons={seasonsBySport['2']}
      sportId='2'
    />,
    Soccer: props => <SportLeagueNav
      sports={sports}
      seasons={seasonsBySport['4']}
      sportId='4'
    />,
    Football: props => <SportLeagueNav
      sports={sports}
      seasons={seasonsBySport['3']}
      sportId='3'
    />
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
  static propTypes = {
    lookups: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props);
  }

  render() {
    const { loading, sports, scoreReporterSeasons } = this.props.lookups;

    console.log("Leagues page loading: " + loading);

    if (loading) return <Loading />;

    const SportsNavigator = createAppContainer(sportsNavigator(sports, scoreReporterSeasons));
    return (
      <SportsNavigator />
    )
  }
}

//this.setState({currentSport: this.props.lookups.sports[currentState.index].id});
//this wont work right in the way it is set up right now, i have to change it for tabs instead of stack nav changes

