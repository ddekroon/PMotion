import React from 'react'
import { createBottomTabNavigator } from 'react-navigation'
import { Image } from 'react-native'

import SportsNavigator from './SportsNavigator'
import ScoreReporter from '../screens/ScoreReporter'
import Registration from '../screens/Registration'

import NavigationProps from '../constants/navigation'
import Images from '../../images/index'

const MainNavigator = createBottomTabNavigator(
  {
    Leagues: {
      screen: SportsNavigator,
      navigationOptions: {
        title: 'Leagues',
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
      screen: ScoreReporter,
      navigationOptions: {
        title: 'Score Reporter',
        tabBarIcon: ({ focused, horizontal, tintColor }) => (
          <Image
            style={{ width: 30, height: 22 }}
            source={focused ? Images.icons.scoresFocused : Images.icons.scores}
          />
        )
      }
    },
    Registration: {
      screen: Registration,
      navigationOptions: {
        title: 'Registration',
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
