import { createMaterialTopTabNavigator } from 'react-navigation'

import Standings from '../components/leagues/Standings'
import Schedule from '../components/leagues/Schedule'
import NavigationProps from '../constants/navigation'

const LeagueNavigator = createMaterialTopTabNavigator(
  {
    Standings: Standings,
    Schedule: Schedule
  },
  {
    ...NavigationProps.tabConfig
  }
)

export default LeagueNavigator

/*
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
    console.log(this.props.navigation)
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
*/
