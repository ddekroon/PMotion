import { createMaterialTopTabNavigator } from 'react-navigation'

import Standings from '../screens/Standings'
import Schedule from '../screens/Schedule'

import NavigationProps from '../constants/navigation'

const LeagueNavigator = createMaterialTopTabNavigator(
  {
    Standings: Standings,
    Schedule: Schedule
  },
  {
    ...NavigationProps.tabConfig
  }
)

export default LeagueNavigator
