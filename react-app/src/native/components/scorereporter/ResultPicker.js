import React from 'react'
import PropTypes from 'prop-types'

import { Item, Picker, Icon } from 'native-base'

import ValidationHelpers from '../../../utils/validationhelpers'
import Enums from '../../../constants/enums'

import Messages from '../common/Messages'

class ScoreReporterResultPicker extends React.Component {
  static propTypes = {
    gameIndex: PropTypes.number.isRequired,
    resultOptions: PropTypes.array.isRequired,
    result: PropTypes.object.isRequired,
    gameString: PropTypes.string.isRequired,
    validate: PropTypes.bool.isRequired,
    league: PropTypes.object.isRequired,
    handleScoreChange: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const {
      gameIndex,
      resultOptions,
      result,
      gameString,
      validate,
      league,
      handleScoreChange
    } = this.props

    const error = !ValidationHelpers.isValidGameResult(result.result)

    return (
      <Item
        style={{
          flex: 1,
          width: '100%',
          flexDirection: 'column',
          borderBottomWidth: 0
        }}
      >
        <Item picker style={{ flex: 1, width: '100%' }} error={error}>
          <Picker
            note={false}
            mode='dropdown'
            iosIcon={<Icon name='ios-arrow-down' />}
            style={{ flex: 1 }}
            textStyle={{ fontWeight: 'normal' }}
            selectedValue={result.result}
            placeholder={gameString}
            onValueChange={(val, idx) => {
              handleScoreChange(gameIndex, 'result', val)
            }}
          >
            {resultOptions
              .filter(curResult => {
                return (
                  curResult.placeholder ||
                  !(
                    curResult.val == Enums.matchResult.Error.val ||
                    (curResult.val == Enums.matchResult.Tied.val &&
                      !league.isTies) ||
                    (curResult.val == Enums.matchResult.Practice.val &&
                      !league.isPracticeGames) ||
                    (curResult.val == Enums.matchResult.Cancelled.val &&
                      !league.isShowCancelOption)
                  )
                )
              })
              .map(curResult => {
                if (curResult.placeholder) {
                  return <Picker.Item key={0} label={gameString} value='' />
                }
                return (
                  <Picker.Item
                    key={curResult.val}
                    label={curResult.text}
                    value={curResult.val}
                  />
                )
              })}
          </Picker>
        </Item>
        {validate && error ? (
          <Item style={{ marginTop: 10, borderBottomWidth: 0, width: '100%' }}>
            <Messages type='error' message='Result is a required field' />
          </Item>
        ) : null}
      </Item>
    )
  }
}

export default ScoreReporterResultPicker
