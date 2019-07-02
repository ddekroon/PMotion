import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { submitScoreSubmission, updateScoreSubmission, resetMatches, resetSubmission } from '../actions/scoreSubmission';
import { fetchLeague } from '../actions/leagues';

class ScoreReporter extends Component {
  constructor(props) {
    super(props);

    this.state = {
      errorMessage: null
    }
  }

  static propTypes = {
    Layout: PropTypes.func.isRequired,
    isLoading: PropTypes.bool.isRequired,
    lookups: PropTypes.object.isRequired,
    leagues: PropTypes.object.isRequired,
    scoreSubmission: PropTypes.object.isRequired,
    updateScoreSubmission: PropTypes.func.isRequired,
    onFormSubmit: PropTypes.func.isRequired,
    getLeague: PropTypes.func.isRequired,
    resetMatches: PropTypes.func.isRequired,
    resetSubmission: PropTypes.func.isRequired
  }

  onFormSubmit = () => {
    const { onFormSubmit } = this.props;
    return submitScoreSubmission()
      .catch((err) => { this.setState({ errorMessage: err }); throw err; });
  }

  render = () => {
    const {
      Layout,
      isLoading,
      lookups,
      getLeague,
      leagues,
      onFormSubmit,
      scoreSubmission,
      updateScoreSubmission,
      resetMatches,
      resetSubmission
    } = this.props;

    const { errorMessage } = this.state;

    return (
      <Layout
        loading={isLoading}
        error={errorMessage}
        sports={lookups.sports}
        seasons={lookups.scoreReporterSeasons}
        onFormSubmit={onFormSubmit}
        getLeague={getLeague}
        leagues={leagues}
        scoreSubmission={scoreSubmission}
        updateScoreSubmission={updateScoreSubmission}
        resetMatches={resetMatches}
        resetSubmission={resetSubmission}
      />
    );
  }
}

const mapStateToProps = state => ({
  leagues: state.leagues || {},
  isLoading: state.status.loading || false,
  lookups: state.lookups || {},
  scoreSubmission: state.scoreSubmission || {}
});

const mapDispatchToProps = {
  updateScoreSubmission: updateScoreSubmission,
  onFormSubmit: submitScoreSubmission,
  getLeague: fetchLeague,
  resetMatches: resetMatches,
  resetSubmission: resetSubmission
};

export default connect(mapStateToProps, mapDispatchToProps)(ScoreReporter);
