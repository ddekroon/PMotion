import React, {useState} from 'react'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import { Container, Content, Text, Picker, View, Icon, Button } from 'native-base'
import { fetchLeague } from '../../../actions/leagues' //Gets the leagues from the web.
import DateTimeHelpers from '../../../utils/datetimehelpers'
import Loading from '../../components/common/Loading'


import {
  submitScoreSubmission,
  updateScoreSubmission,
  resetMatches,
  resetSubmission
} from '../../../actions/scoreSubmission'

class PickLeagues extends React.Component { 
  
  static propTypes = {
    seasons: PropTypes.object.isRequired,
    isLoading: PropTypes.bool.isRequired,
    leagues: PropTypes.object.isRequired,
    scoreSubmission: PropTypes.object.isRequired,
    updateScoreSubmission: PropTypes.func.isRequired,
    getLeague: PropTypes.func.isRequired //getLeague is a function!
  }
  
  constructor(props) {
    super(props)
  }

  state = {league: ''}
  updateLeague = (league) => {
      this.setState({ league: league })
  }

  render() {
    if (seasons == null) {
      console.log("Error loading seasons")
    }

    const {
      loading,
      seasons
    } = this.props


    return (
      <View>
        <Picker
          placeholder="League"
          note={false}
          mode="dropdown"
          iosIcon={<Icon name="arrow-down" />}
          selectedValue = {this.state.league}
          onValueChange={this.updateLeague}
          style = {{
            borderWidth: 1,
            alignItems: 'center',
            flexDirection:'row',
            justifyContent: 'center',
            //Should be centered :/
          }}
        >

          <Picker.Item key={0} label={'League'} value={''} />
          {seasons[this.props.sport][0].leagues.map(curLeague => {
            var leagueName =
              curLeague.name +
              ' - ' +
              DateTimeHelpers.getDayString(curLeague.dayNumber)

            return (
              <Picker.Item
                key={curLeague.id}
                label={leagueName}
                value={curLeague.id}
              />
            )
          })}
        </Picker>
      </View>
    )
  }
}

const mapStateToProps = state => ({
  seasons: state.lookups.scoreReporterSeasons || [],
  leagues: state.leagues || {},
  isLoading: state.status.loading || false,
  scoreSubmission: state.scoreSubmission || {}
})

const mapDispatchToProps = { 
  updateScoreSubmission: updateScoreSubmission,
  onFormSubmit: submitScoreSubmission,
  getLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(PickLeagues)
