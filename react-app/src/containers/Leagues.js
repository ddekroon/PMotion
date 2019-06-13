import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { getLookups } from '../actions/lookups';

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
  }

  componentDidMount = () => {
    const { fetchData } = this.props;
    fetchData();
  }

  render = () => {
    const { Layout, lookups } = this.props;

    return <Layout lookups={lookups} />;
  }
}

const mapStateToProps = state => ({
  lookups: state.lookups || {},
});

const mapDispatchToProps = {
  fetchData: getLookups,
};

export default connect(mapStateToProps, mapDispatchToProps)(Leagues);
