import React from 'react'
import PropTypes from 'prop-types'
import DateTimeHelpers from '../../../utils/datetimehelpers'

import { Item, Picker, Icon } from 'native-base'

import Messages from '../common/Messages'

class ScoreReporterLeaguePicker extends React.Component {
  static propTypes = {
    scoreSubmission: PropTypes.object.isRequired,
    seasons: PropTypes.object.isRequired,
    onLeagueUpdated: PropTypes.func.isRequired,
    validate: PropTypes.bool.isRequired
  }

  constructor(props) {
    super(props)
  }

  renderLeagueOptions = (scoreSubmission, seasons) => {
    if (seasons[scoreSubmission.sportId] == null) {
      return <Picker.Item key={0} label='League' value='' />
    }

    var isMultipleSeasons =
      scoreSubmission.sportId != ''
        ? seasons[scoreSubmission.sportId].length > 1
        : false

    var leagueOptions = [{ placeholder: true }]

    seasons[scoreSubmission.sportId].forEach(curSeason => {
      if (curSeason.leagues == null) {
        return
      }

      curSeason.leagues.forEach(curLeague => leagueOptions.push(curLeague))
    })

    var toReturn = leagueOptions.map(curLeague => {
      if (curLeague.placeholder) {
        return <Picker.Item key={0} label='League' value='' />
      }

      var leagueName =
        curLeague.name +
        ' - ' +
        DateTimeHelpers.getDayString(curLeague.dayNumber)
      if (isMultipleSeasons) {
        leagueName = leagueName + ' - ' + curSeason.name
      }

      return (
        <Picker.Item
          key={curLeague.id}
          label={leagueName}
          value={curLeague.id}
        />
      )
    })

    return toReturn
  }

  render() {
    const { scoreSubmission, seasons, validate, onLeagueUpdated } = this.props
    const { leagueId } = scoreSubmission
    const error = leagueId == ''

    return (
      <Item
        style={{
          flex: 1,
          width: '100%',
          flexDirection: 'column',
          borderBottomWidth: 0
        }}
      >
        <Item picker error={error} style={{ flex: 1, width: '100%' }}>
          <Picker
            note={false}
            mode='dropdown'
            iosIcon={<Icon name='ios-arrow-down' />}
            iosHeader='Select One'
            style={{ flex: 1 }}
            selectedValue={leagueId}
            textStyle={{ fontWeight: 'normal' }}
            onValueChange={(val, index) => onLeagueUpdated(val)}
          >
            {this.renderLeagueOptions(scoreSubmission, seasons)}
          </Picker>
        </Item>
        {validate && error ? (
          <Item style={{ marginTop: 10, borderBottomWidth: 0 }}>
            <Messages type='error' message='League is a required field' />
          </Item>
        ) : null}
      </Item>
    )
  }
}

export default ScoreReporterLeaguePicker
