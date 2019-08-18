import React from 'react'
import PropTypes from 'prop-types'
import { Content, Text, Item, Picker, Icon } from 'native-base'
import Loading from './Loading'
import ValidationHelpers from '../../../utils/validationhelpers'

class TeamPicker extends React.Component {
  static propTypes = {
    isValid: PropTypes.bool,
    loading: PropTypes.bool.isRequired,
    label: PropTypes.string,
    teams: PropTypes.array.isRequired,
    curTeamId: PropTypes.string,
    excludeTeamId: PropTypes.string,
    onTeamUpdated: PropTypes.func.isRequired
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
      isValid,
      label,
      loading,
      teams,
      curTeamId,
      onTeamUpdated,
      excludeTeamId
    } = this.props

    var isTeams = !loading && teams != null && teams.length > 0
    var options = [{ placeholder: true }].concat(teams)

    return (
      <Item
        style={{ flex: 1, width: '100%' }}
        picker={isTeams}
        error={isTeams && !ValidationHelpers.isValidId(curTeamId)}
      >
        {!loading && isTeams && (
          <Picker
            note={false}
            mode="dropdown"
            iosIcon={<Icon name="ios-arrow-down" />}
            style={{ flex: 1 }}
            textStyle={{ fontWeight: 'normal' }}
            selectedValue={curTeamId}
            placeholder={label}
            onValueChange={(val, index) => onTeamUpdated(val)}
          >
            {options
              .filter(
                curTeam => curTeam.placeholder || curTeam.id != excludeTeamId
              )
              .map(curTeam => {
                if (curTeam.placeholder) {
                  return <Picker.Item key={0} label={label} value={''} />
                }

                return (
                  <Picker.Item
                    key={curTeam.id}
                    label={curTeam.name}
                    value={curTeam.id}
                  />
                )
              })}
          </Picker>
        )}

        {!loading && !isTeams && (
          <Content padder>
            <Text style={{ fontStyle: 'italic' }}>No teams to select</Text>
          </Content>
        )}

        {loading && (
          <Content padder>
            <Loading />
          </Content>
        )}
      </Item>
    )
  }
}

export default TeamPicker
