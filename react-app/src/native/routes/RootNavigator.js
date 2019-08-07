import React from 'react'
import { createAppContainer, createStackNavigator } from 'react-navigation'

import MainNavigator from './MainNavigator'
import LeaguePage from '../components/leagues/League'

import NavigationProps from '../constants/navigation'

const RootStack = createStackNavigator(
  {
    Main: MainNavigator,
    League: LeaguePage
  },
  {
    initialRouteName: 'Main',
    mode: 'modal',
    defaultNavigationOptions: NavigationProps.navbarProps
  }
)

const RootContainer = createAppContainer(RootStack)

export default class RootNavigator extends React.Component {
  render() {
    return <RootContainer />
  }
}
