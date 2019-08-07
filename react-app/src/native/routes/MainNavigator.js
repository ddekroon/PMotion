import React from 'react'
import { createBottomTabNavigator, createAppContainer } from 'react-navigation'

import LeaguesPage from '../components/pages/Leagues'
import ScoreReporterPage from '../components/pages/ScoreReporter'
import ProfilePage from '../components/pages/Profile'

import NavigationProps from '../constants/navigation'

const RootStack = createBottomTabNavigator(
  {
    Leagues: LeaguesPage,
    ScoreReporter: ScoreReporterPage,
    Profile: ProfilePage
  },
  {
    ...NavigationProps.bottomTabConfig,
    initialRouteName: 'Leagues'
  }
)

const MainContainer = createAppContainer(RootStack)

export default class MainNavigator extends React.Component {
  static navigationOptions = {
    ...NavigationProps.screenProps
  }

  render() {
    return <MainContainer />
  }
}
