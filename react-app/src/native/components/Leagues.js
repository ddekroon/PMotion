import React from 'react';
import PropTypes from 'prop-types';
import { createAppContainer, createMaterialTopTabNavigator } from 'react-navigation';
import Loading from './Loading'
import SportLeagues from '../components/SportLeagues';
import NavigationProps from '../constants/navigation';

const sportsNavigator = (sports, seasonsBySport) => createMaterialTopTabNavigator(
  {
    Ultimate: props => <SportLeagues
      sports={sports}
      seasons={seasonsBySport['1']}
      sportId='1'
    />,
    VolleyBall: props => <SportLeagues
      sports={sports}
      seasons={seasonsBySport['2']}
      sportId='2'
    />,
    Soccer: props => <SportLeagues
      sports={sports}
      seasons={seasonsBySport['4']}
      sportId='4'
    />,
    Football: props => <SportLeagues
      sports={sports}
      seasons={seasonsBySport['3']}
      sportId='3'
    />
  },

  {
    ...NavigationProps.tabConfig
  }
);

export default class Leagues extends React.Component {
  static propTypes = {
    lookups: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props);
  }

  componentWillUnmount() {
    console.log("Unmounting leagues component");
  }

  render() {
    const { loading, sports, scoreReporterSeasons } = this.props.lookups;

    if (loading) return <Loading />;

    const SportsNavigator = createAppContainer(sportsNavigator(sports, scoreReporterSeasons));
    return (
      <SportsNavigator detached={true}/>
    )
  }
}


