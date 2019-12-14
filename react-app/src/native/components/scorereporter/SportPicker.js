import React from 'react'
import PropTypes from 'prop-types'

import { Item, Picker, Icon } from 'native-base'

import Messages from '../common/Messages'

class ScoreReporterSportPicker extends React.Component {
  static propTypes = {
    sportId: PropTypes.string.isRequired,
    sportOptions: PropTypes.array.isRequired,
    onSportUpdated: PropTypes.func.isRequired,
    validate: PropTypes.bool.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { sportId, sportOptions, validate, onSportUpdated } = this.props
    const error = sportId == ''

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
            textStyle={{ fontWeight: 'normal' }}
            selectedValue={sportId}
            onValueChange={(val, index) => onSportUpdated(val)}
          >
            {sportOptions.map(curSport => (
              <Picker.Item
                key={curSport.id}
                label={curSport.name}
                value={curSport.id}
              />
            ))}
          </Picker>
        </Item>
        {validate && error ? (
          <Item style={{ marginTop: 10, borderBottomWidth: 0 }}>
            <Messages type='error' message='Sport is a required field' />
          </Item>
        ) : null}
      </Item>
    )
  }
}

export default ScoreReporterSportPicker
