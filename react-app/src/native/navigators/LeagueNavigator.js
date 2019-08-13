import { createMaterialTopTabNavigator } from 'react-navigation'

import Standings from '../screens/Standings'
import Schedule from '../screens/Schedule'
import Teams from '../screens/Teams'

import NavigationProps from '../constants/navigation'

const LeagueNavigator = createMaterialTopTabNavigator(
  {
    Schedule: Schedule,
    Standings: Standings,
    Teams: Teams
  },
  {
    ...NavigationProps.tabConfig
  }
)

export default LeagueNavigator
