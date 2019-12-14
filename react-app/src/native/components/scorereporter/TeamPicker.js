import React from 'react'
import PropTypes from 'prop-types'

import { Content, Item, Text } from 'native-base'

import TeamPicker from '../common/TeamPicker'
import Messages from '../common/Messages'

class ScoreReporterTeamPicker extends React.Component {
  static propTypes = {
    curTeamId: PropTypes.string.isRequired,
    teams: PropTypes.array.isRequired,
    onTeamUpdated: PropTypes.func.isRequired,
    validate: PropTypes.bool.isRequired,
    loading: PropTypes.bool.isRequired,
    label: PropTypes.string,
    excludeTeamId: PropTypes.string
  }

  static defaultProps = {
    isValid: true,
    curTeamId: '',
    excludeTeamId: '',
    label: 'Team'
  }

  constructor(props) {
    super(props)
  }

  render() {
    const {
      curTeamId,
      teams,
      validate,
      onTeamUpdated,
      loading,
      label,
      excludeTeamId
    } = this.props
    const error = curTeamId == ''

    var errorMessage = label + ' is a required field'

    return (
      <Item
        style={{
          flex: 1,
          width: '100%',
          flexDirection: 'column',
          borderBottomWidth: 0
        }}
      >
        <TeamPicker
          loading={loading}
          teams={teams}
          curTeamId={curTeamId}
          onTeamUpdated={onTeamUpdated}
          label={label}
          excludeTeamId={excludeTeamId}
        />
        {validate && error ? (
          <Item style={{ marginTop: 10, borderBottomWidth: 0 }}>
            <Messages type='error' message={errorMessage} />
          </Item>
        ) : null}
      </Item>
    )
  }
}

export default ScoreReporterTeamPicker
