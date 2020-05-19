import React from 'react'
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs'

import SportLeagues from '../screens/SportLeagues'
import NavigationProps from '../constants/navigation'

const Tab = createMaterialTopTabNavigator()

function SportsNavigator (navigation) {
  return (
    <Tab.Navigator {...NavigationProps.tabConfig}>
      <Tab.Screen name="Ultimate">
        {() => <SportLeagues screenProps={{ sportId: 1, navigation }} />}
      </Tab.Screen>
      <Tab.Screen name="VolleyBall">
        {() => <SportLeagues screenProps={{ sportId: 2, navigation }} />}
      </Tab.Screen>
      <Tab.Screen name="Soccer">
        {() => <SportLeagues screenProps={{ sportId: 4, navigation }} />}
      </Tab.Screen>
      <Tab.Screen name="Football">
        {() => <SportLeagues screenProps={{ sportId: 3, navigation }} />}
      </Tab.Screen>
    </Tab.Navigator>
  )
}

export default SportsNavigator
