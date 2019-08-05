import React from 'react'
import { createBottomTabNavigator, createAppContainer } from 'react-navigation'

import LeaguesPage from './pages/Leagues'
import ScoreReporterPage from './pages/ScoreReporter'
import ProfilePage from './pages/Profile'

import NavigationProps from '../constants/navigation'

const RootStack = createBottomTabNavigator(
  {
    Leagues: LeaguesPage,
    ScoreReporter: ScoreReporterPage,
    Profile: ProfilePage
  },
  {
    ...NavigationProps.tabConfig,
    initialRouteName: 'Leagues'
  }
)

const AppContainer = createAppContainer(RootStack)

export default class AppNavigator extends React.Component {
  render() {
    return <AppContainer />
  }
}
