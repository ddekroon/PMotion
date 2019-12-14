import React from 'react'
import PropTypes from 'prop-types'

import { Item, Picker, Icon } from 'native-base'

import Messages from '../common/Messages'

class ScoreReporterSpiritPicker extends React.Component {
  static propTypes = {
    spiritScore: PropTypes.string.isRequired,
    validate: PropTypes.bool.isRequired,
    onValueChange: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { spiritScore, validate, onValueChange } = this.props
    const error = spiritScore == ''

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
            style={{ flex: 1 }}
            textStyle={{ fontWeight: 'normal' }}
            selectedValue={spiritScore}
            placeholder='Spirit Score'
            onValueChange={onValueChange}
          >
            <Picker.Item label='Spirit Score' value='' />
            <Picker.Item label='5' value='5' />
            <Picker.Item label='4.5' value='4.5' />
            <Picker.Item label='4' value='4' />
            <Picker.Item label='3.5' value='3.5' />
            <Picker.Item label='3' value='3' />
            <Picker.Item label='2.5' value='2.5' />
            <Picker.Item label='2' value='2' />
            <Picker.Item label='1.5' value='1.5' />
            <Picker.Item label='1' value='1' />
          </Picker>
        </Item>
        {validate && error ? (
          <Item style={{ marginTop: 10, borderBottomWidth: 0, width: '100%' }}>
            <Messages type='error' message='Spirit score is a required field' />
          </Item>
        ) : null}
      </Item>
    )
  }
}

export default ScoreReporterSpiritPicker
