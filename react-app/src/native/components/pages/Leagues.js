import React from 'react'
import PropTypes from 'prop-types'
import {
  createAppContainer,
  createMaterialTopTabNavigator
} from 'react-navigation'
import { Image } from 'react-native'

import Loading from '../common/Loading'
import SportLeagues from '../leagues/LeaguesList'
import NavigationProps from '../../constants/navigation'
import { connect } from 'react-redux'

import { getLookups } from '../../../actions/lookups'
import { fetchLeague } from '../../../actions/leagues'

const sportsNavigator = (sports, seasonsBySport) =>
  createMaterialTopTabNavigator(
    {
      Ultimate: props => (
        <SportLeagues
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['1']}
          sportId="1"
        />
      ),
      VolleyBall: props => (
        <SportLeagues
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['2']}
          sportId="2"
        />
      ),
      Soccer: props => (
        <SportLeagues
          navigation={props.navigation}
          sports={sports}
          seasons={seasonsBySport['4']}
          sportId="4"
        />
      ),
      Football: props => (
        <SportLeagues
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
    const { fetchData } = this.props
    fetchData()
  }

  componentWillUnmount() {
    console.log('Unmounting leagues component')
  }

  render() {
    const { loading, sports, scoreReporterSeasons } = this.props.lookups
    const { navigation } = this.props

    console.log(navigation)

    if (loading) return <Loading />

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
  fetchData: getLookups,
  getLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Leagues)
