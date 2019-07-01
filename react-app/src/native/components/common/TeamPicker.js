import React from 'react';
import PropTypes from 'prop-types';
import {
  Content, Text, Item, Picker, Icon
} from 'native-base';
import Loading from '../Loading';

class TeamPicker extends React.Component {
  static propTypes = {
    isValid: PropTypes.bool,
    loading: PropTypes.bool.isRequired,
    teams: PropTypes.array.isRequired,
    curTeamId: PropTypes.string,
    onTeamUpdated: PropTypes.func.isRequired,
  }

  static defaultProps = {
    isValid: true,
    curTeamId: 0
  }

  constructor(props) {
    super(props);
  }

  render() {
    const { isValid, loading, teams, curTeamId, onTeamUpdated } = this.props;

    var isTeams = !loading && teams != null && teams.length > 0;

    return (
      <Item picker={isTeams}>
        {
          !loading && isTeams &&
          <Picker
            note={false}
            mode="dropdown"
            iosIcon={<Icon name="arrow-down" />}
            style={{ flex: 1 }}
            selectedValue={curTeamId}
            placeholder="Team"
            onValueChange={(val, index) => onTeamUpdated(val)}
          >
            <Picker.Item key={0} label={'Team'} value={''} />
            {
              teams.map((curTeam) => {
                return <Picker.Item key={curTeam.id} label={curTeam.name} value={curTeam.id} />
              })
            }
          </Picker>
        }

        {
          !loading && !isTeams &&
          <Content padder><Text style={{ fontStyle: "italic" }}>No teams to select</Text></Content>
        }

        {loading &&
          <Content padder>
            <Loading />
          </Content>
        }
      </Item>
    );
  }
}

export default TeamPicker;
