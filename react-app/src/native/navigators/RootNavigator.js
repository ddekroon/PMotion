import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { NavigationContainer } from '@react-navigation/native'
import { createStackNavigator } from '@react-navigation/stack'

import MainNavigator from './MainNavigator'
import LeagueNavigator from './LeagueNavigator'
import Loading from '../components/common/Loading'
import TeamPage from '../components/leagues/TeamPage'
import SchedulePage from '../screens/Schedule'
import Maps from '../screens/Maps'

import NavigationProps from '../constants/navigation'
import { getLookups } from '../../actions/lookups'
import IndividualRegister from '../screens/IndividualRegister'
import GroupRegister from '../screens/GroupRegister'
import Login from '../screens/todo/Login'

const Stack = createStackNavigator();

class RootNavigator extends React.Component {
  static propTypes = {
    isFetching: PropTypes.bool.isRequired,
    fetchLookups: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    props.fetchLookups()
  }

  render() {
    const { isFetching } = this.props

    if (isFetching) return <Loading />

    return (
    <NavigationContainer>
      <Stack.Navigator
        initialRouteName="Main"
        mode="stack"
        defaultNavigationOptions={NavigationProps.navbarProps}
      >
        <Stack.Screen 
          name="Main"
          component={MainNavigator} 
          navigationOptions={NavigationProps.mainTitle}
          />

        <Stack.Screen 
          name="League"
          component={LeagueNavigator} 
          options={({ route }) => ({
            title: route.params.title ?? 'League'
          })}
          />

        <Stack.Screen 
          name="Team"
          component={TeamPage}
        />

        <Stack.Screen 
          name="Schedule"
          component={SchedulePage}
        />

        <Stack.Screen 
          name="Maps"
          component={Maps}
        />

        <Stack.Screen
          name="IndividualRegister"
          component={IndividualRegister}
        />

        <Stack.Screen
          name="Login"
          component={Login}
        />
        
        <Stack.Screen
          name="GroupRegister"
          component={GroupRegister}
        />
        
      </Stack.Navigator>
    </NavigationContainer>
    )
  }
}

const mapStateToProps = state => ({
  isFetching: state.lookups.isFetching
})

const mapDispatchToProps = {
  fetchLookups: getLookups
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RootNavigator)
