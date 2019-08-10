import React from 'react'
import { createMaterialTopTabNavigator } from 'react-navigation'

import SportLeagues from '../screens/SportLeagues'
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
