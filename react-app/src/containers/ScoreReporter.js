import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { submitScores, updateScoreSubmission } from '../actions/scoreSubmission';
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
    getLeague: PropTypes.func.isRequired
  }

  /*onFormSubmit = (data) => {
    const { onFormSubmit } = this.props;
    return onFormSubmit(data)
      .catch((err) => { this.setState({ errorMessage: err }); throw err; });
  }*/

  render = () => {
    const {
      Layout,
      isLoading,
      lookups,
      getLeague,
      leagues,
      onFormSubmit,
      scoreSubmission,
      updateScoreSubmission
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
  onFormSubmit: submitScores,
  getLeague: fetchLeague
};

export default connect(mapStateToProps, mapDispatchToProps)(ScoreReporter);
