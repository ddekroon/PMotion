import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { createAppContainer, createStackNavigator } from 'react-navigation'

import MainNavigator from './MainNavigator'
import LeagueNavigator from './LeagueNavigator'
import Loading from '../components/common/Loading'
import TeamPage from '../components/leagues/TeamPage'

import NavigationProps from '../constants/navigation'
import { getLookups } from '../../actions/lookups'

const RootStack = createStackNavigator(
  {
    Main: {
      screen: MainNavigator,
      navigationOptions: NavigationProps.mainTitle
    },
    League: {
      screen: LeagueNavigator,
      navigationOptions: ({ navigation }) => ({
        title: navigation.getParam('title', 'League')
      })
    },
    Team: {
     screen: TeamPage
    }
  },
  {
    initialRouteName: 'Main',
    mode: 'Modal',
    defaultNavigationOptions: NavigationProps.navbarProps
  }
)

const RootContainer = createAppContainer(RootStack)

class RootNavigator extends React.Component {
  static propTypes = {
    isFetching: PropTypes.bool.isRequired,
    fetchLookups: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    props.fetchLookups()
  }

  render() {
    const { isFetching } = this.props

    if (isFetching) return <Loading />

    return <RootContainer />
  }
}

const mapStateToProps = state => ({
  isFetching: state.lookups.isFetching
})

const mapDispatchToProps = {
  fetchLookups: getLookups
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RootNavigator)
