import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { fetchLeague } from '../../actions/leagues';
import Loading from './Loading';
import { createAppContainer, createMaterialTopTabNavigator } from 'react-navigation';
import Standings from '../components/Standings';
import Schedule from '../components/Schedule';
import NavigationProps from '../constants/navigation';


const leagueNavigator = (league) => createMaterialTopTabNavigator(
  {
    Standings: props => <Standings
      league={league}
    />,
    Schedule: props => <Schedule
      league={league}
    />,
  },
  {
    ...NavigationProps.tabConfig
  }
);


class LeaguePage extends React.Component {
  static propTypes = {
    leagues: PropTypes.object.isRequired,
    leagueId: PropTypes.string,
    getLeague: PropTypes.func.isRequired
  }

  static defaultProps = {
    leagueId: null
  }

  constructor(props) {
    super(props);
    props.getLeague(this.props.leagueId);
  }

  render() {
    const { leagues, leagueId } = this.props;

    const league = leagues[leagueId];

    if (league == null || league.isFetching) return <Loading />;


    const LeagueNavigator = createAppContainer(leagueNavigator(league));
    return (
      <LeagueNavigator />
    )
  }
}

const mapStateToProps = state => ({
  leagues: state.leagues || {},
});

const mapDispatchToProps = {
  getLeague: fetchLeague,
};

export default connect(mapStateToProps, mapDispatchToProps)(LeaguePage);

/*calendar, podium, people */

