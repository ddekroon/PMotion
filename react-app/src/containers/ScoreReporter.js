import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { submitScores } from '../actions/scoreReporter';

class ScoreReporter extends Component {
  constructor(props) {
    super(props);

    this.state = {
      errorMessage: null,
      lookups: {}
    }
  }
  static propTypes = {
    Layout: PropTypes.func.isRequired,
    onFormSubmit: PropTypes.func.isRequired,
    isLoading: PropTypes.bool.isRequired,
    lookups: PropTypes.object.isRequired
  }

  onFormSubmit = (data) => {
    const { onFormSubmit } = this.props;
    return onFormSubmit(data)
      .catch((err) => { this.setState({ errorMessage: err }); throw err; });
  }

  render = () => {
    const {
      Layout,
      isLoading,
      lookups
    } = this.props;

    const { errorMessage } = this.state;

    return (
      <Layout
        loading={isLoading}
        error={errorMessage}
        sports={lookups.sports}
        seasons={lookups.scoreReporterSeasons}
        onFormSubmit={this.onFormSubmit}
      />
    );
  }
}

const mapStateToProps = state => ({
  member: state.member || {},
  isLoading: state.status.loading || false,
  lookups: state.lookups || {},
});

const mapDispatchToProps = {
  onFormSubmit: submitScores,
};

export default connect(mapStateToProps, mapDispatchToProps)(ScoreReporter);
