import React from 'react'
import PropTypes from 'prop-types'
import {
  createAppContainer,
  createMaterialTopTabNavigator
} from 'react-navigation'
import { Image } from 'react-native'

import LeaguesList from '../leagues/LeaguesList'
import NavigationProps from '../../constants/navigation'
import { connect } from 'react-redux'

import { getLookups } from '../../../actions/lookups'
import { fetchLeague } from '../../../actions/leagues'

const sportsNavigator = (sports, seasonsBySport) =>
  createMaterialTopTabNavigator(
    {
      Ultimate: props => (
        <LeaguesList
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['1']}
          sportId="1"
        />
      ),
      VolleyBall: props => (
        <LeaguesList
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['2']}
          sportId="2"
        />
      ),
      Soccer: props => (
        <LeaguesList
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['4']}
          sportId="4"
        />
      ),
      Football: props => (
        <LeaguesList
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['3']}
          sportId="3"
        />
      )
    },
    {
      ...NavigationProps.tabConfig
    }
  )

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
