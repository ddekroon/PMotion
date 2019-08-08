import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import {
  createAppContainer,
  createMaterialTopTabNavigator
} from 'react-navigation'
import { View, Button, Text, Icon } from 'native-base'

import { fetchLeague } from '../../../actions/leagues'

import Loading from '../common/Loading'
import Standings from './Standings'
import Schedule from './Schedule'
import NavigationProps from '../../constants/navigation'

const leagueNavigator = league =>
  createMaterialTopTabNavigator(
    {
      Standings: props => <Standings league={league} />,
      Schedule: props => <Schedule league={league} />
    },
    {
      ...NavigationProps.tabConfig
    }
  )

class LeaguePage extends React.Component {
  static propTypes = {
    leagues: PropTypes.object.isRequired,
    leagueId: PropTypes.string,
    getLeague: PropTypes.func.isRequired
  }

  static defaultProps = {
    leagueId: '1640'
  }

  constructor(props) {
    super(props)
    props.getLeague(this.props.leagueId)
  }

  render() {
    const { leagues, leagueId } = this.props
    const league = leagues[leagueId]

    if (league == null || league.isFetching) return <Loading />

    const LeagueNavigator = createAppContainer(leagueNavigator(league))
    //return <LeagueNavigator detached={true} />

    return (
      <View>
        <Button onPress={() => this.props.navigation.navigate('Main')}>
          <Text>
            Click Me{' '}
            <Icon
              style={{ fontSize: 20, color: 'white' }}
              name="arrow-forward"
            />
          </Text>
        </Button>
      </View>
    )
  }
}

const mapStateToProps = state => ({
  leagues: state.leagues || {}
})

const mapDispatchToProps = {
  getLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(LeaguePage)
