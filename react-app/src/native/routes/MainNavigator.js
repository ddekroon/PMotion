import React from 'react'
import { createBottomTabNavigator } from 'react-navigation'
import { Image } from 'react-native'

import SportsNavigator from './SportsNavigator'
import ScoreReporterPage from '../components/pages/ScoreReporter'
import ProfilePage from '../components/pages/Profile'

import NavigationProps from '../constants/navigation'
import Images from '../../images/index'

const MainNavigator = createBottomTabNavigator(
  {
    Leagues: {
      screen: SportsNavigator,
      navigationOptions: {
        tabBarIcon: ({ focused, horizontal, tintColor }) => (
          <Image
            style={{ width: 20, height: 20 }}
            source={
              focused ? Images.icons.leaguesFocused : Images.icons.leagues
            }
          />
        )
      }
    },
    ScoreReporter: {
      screen: ScoreReporterPage,
      navigationOptions: {
        tabBarIcon: ({ focused, horizontal, tintColor }) => (
          <Image
            style={{ width: 30, height: 22 }}
            source={focused ? Images.icons.scoresFocused : Images.icons.scores}
          />
        )
      }
    },
    Profile: {
      screen: ProfilePage,
      navigationOptions: {
        tabBarIcon: ({ focused, horizontal, tintColor }) => (
          <Image
            style={{ width: 20, height: 20 }}
            source={
              focused
                ? Images.icons.registrationFocused
                : Images.icons.registration
            }
          />
        )
      }
    }
  },
  {
    ...NavigationProps.bottomTabConfig
  }
)

export default MainNavigator

/*
const MainContainer = createAppContainer(RootStack)

export default class MainNavigator extends React.Component {
  static navigationOptions = {
    ...NavigationProps.screenProps
  }

  render() {
    return <MainContainer />
  }
}
*/
