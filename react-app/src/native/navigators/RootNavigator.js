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
import Registration from '../screens/Registration'

import NavigationProps from '../constants/navigation'
import { getLookups } from '../../actions/lookups'
import IndividualRegister from '../screens/IndividualRegister'
import Login from '../screens/Login'
import PickSport from '../components/SportPicker'
import Previousleagues from '../screens/PreviousLeagues'
import RegisterTeam from '../screens/RegisterTeam'
import Profile from '../screens/Profile'
import Waivers from '../screens/Waivers'
import NewUser from '../screens/NewUser'
import ForgotPassword from '../screens/ForgotPassword'

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
        headerMode="float"
        screenOptions={NavigationProps.navbarProps}
      >
        <Stack.Screen 
          name="Main"
          component={MainNavigator} 
          options={NavigationProps.mainTitle}
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
          options={({ route }) => ({
            title: route.params.title ?? 'Team'
          })}
        />
        
        <Stack.Screen 
          name="RegistrationScreen"
          component={Registration}
          navigationOptions=  {{
            title: 'Registration',
            headerLeft: null
        }}
        />

        <Stack.Screen 
          name="Schedule"
          component={SchedulePage}
          options={({ route }) => ({
            title: route.params.title ?? 'Schedule'
          })}
        />

        <Stack.Screen 
          name="Maps"
          component={Maps}
        />

        <Stack.Screen
          name="Login"
          component={Login}
        />

        <Stack.Screen
          name="IndividualRegister"
          component={IndividualRegister}
        />

        <Stack.Screen
          name ='PickSport'
          component={PickSport}
        />

        <Stack.Screen
          name='Previousleagues'
          component={Previousleagues}
        />

        <Stack.Screen
          name='RegisterNewTeam'
          component={RegisterTeam}
        />
        
        <Stack.Screen
          name='profile'
          component={Profile}
        />
        
        <Stack.Screen
          name='waivers'
          component={Waivers}
        />

        <Stack.Screen
          name='NewUser'
          component={NewUser}
          //navigationOptions={{gesturesEnable:false}}
        />

        <Stack.Screen
          name='ForgotPassword'
          component={ForgotPassword}
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
