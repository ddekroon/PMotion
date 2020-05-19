import React from 'react'
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs'

import Standings from '../screens/Standings'
import Schedule from '../screens/Schedule'

import NavigationProps from '../constants/navigation'

const Tab = createMaterialTopTabNavigator()

function LeagueNavigator (route) {
  return (
    <Tab.Navigator {...NavigationProps.tabConfig}>
      <Tab.Screen name="Standings">
        {() => <Standings {...route } /> }
      </Tab.Screen>
      <Tab.Screen name="Schedule">
        {() => <Schedule {...route } /> }
      </Tab.Screen>
    </Tab.Navigator>
  )
}

export default LeagueNavigator
