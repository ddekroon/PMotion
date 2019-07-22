import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { getLookups } from '../actions/lookups';
import { fetchLeague } from '../actions/leagues';


class Leagues extends Component {
  static propTypes = {
    Layout: PropTypes.func.isRequired,
    fetchData: PropTypes.func.isRequired,
    lookups: PropTypes.shape({
      loading: PropTypes.bool.isRequired,
      seasonsAvailableRegistration: PropTypes.array.isRequired,
      seasonsAvailableScoreReporter: PropTypes.array.isRequired,
      sports: PropTypes.array.isRequired
    }).isRequired,
    leagues: PropTypes.object.isRequired,
    leagueId: PropTypes.string,
    getLeague: PropTypes.func.isRequired
  }

  static defaultProps = {
    leagueId: null
  }

  componentDidMount = () => {
    const { fetchData } = this.props;
    fetchData();
  }

  render = () => {
    const { Layout, lookups, leagues, leagueId, getLeague } = this.props;

    return <Layout
      lookups={lookups}
      leagues={leagues}
      leagueId={leagueId}
      getLeague={getLeague}
    />;
  }
}

//read data from store
const mapStateToProps = state => ({
  lookups: state.lookups || {},
  leagues: state.leagues || {}
});

//map actions to components
const mapDispatchToProps = {
  fetchData: getLookups,
  getLeague: fetchLeague
};

export default connect(mapStateToProps, mapDispatchToProps)(Leagues);
