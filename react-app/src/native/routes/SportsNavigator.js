import React from 'react'
import { createMaterialTopTabNavigator } from 'react-navigation'

import SportLeagues from '../components/leagues/SportLeagues'
import NavigationProps from '../constants/navigation'

const SportsNavigator = createMaterialTopTabNavigator(
  {
    Ultimate: {
      screen: ({ navigation }) => (
        <SportLeagues screenProps={{ sportId: 1, navigation }} />
      )
    },
    VolleyBall: {
      screen: ({ navigation }) => (
        <SportLeagues screenProps={{ sportId: 2, navigation }} />
      )
    },
    Soccer: {
      screen: ({ navigation }) => (
        <SportLeagues screenProps={{ sportId: 4, navigation }} />
      )
    },
    Football: {
      screen: ({ navigation }) => (
        <SportLeagues screenProps={{ sportId: 3, navigation }} />
      )
    }
  },
  {
    ...NavigationProps.tabConfig
  }
)

export default SportsNavigator
/*
class Leagues extends React.Component {
  static propTypes = {
    lookups: PropTypes.object.isRequired
  }

  static navigationOptions = {
    title: 'Leagues',
    tabBarIcon: () => (
      <Image
        style={{ width: 20, height: 20 }}
        source={require('../../../images/icons/leagues.png')}
      />
    )
  }

  constructor(props) {
    super(props)
  }

  componentDidMount = () => {
    console.log('Leagues component did mount')
  }

  componentWillUnmount() {
    console.log('Unmounting leagues component')
  }

  render() {
    const { sports, scoreReporterSeasons } = this.props.lookups
    const { navigation } = this.props

    console.log('Render Leagues')

    const SportsNavigator = createAppContainer(
      sportsNavigator(sports, scoreReporterSeasons)
    )
    return <SportsNavigator />
  }
}

//read data from store
const mapStateToProps = state => ({
  lookups: state.lookups || {},
  leagues: state.leagues || {}
})

//map actions to components
const mapDispatchToProps = {
  getLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Leagues)
*/
