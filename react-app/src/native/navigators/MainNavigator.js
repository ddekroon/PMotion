import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Image, Text } from 'react-native'

import SportsNavigator from './SportsNavigator'
import ScoreReporter from '../screens/ScoreReporter'
import Registration from '../screens/Registration'
import Login from '../screens/Login'

import NavigationProps from '../constants/navigation'
import Images from '../../images/index'
import { connect } from 'react-redux';


const Tab = createBottomTabNavigator()

function MainNavigator (LoginInfo) {
  return (
    
    <Tab.Navigator {...NavigationProps.bottomTabConfig}>
      <Tab.Screen name="Leagues" component={SportsNavigator}
        navigationOptions={{
          title: 'Leagues',
          tabBarIcon: ({ focused, horizontal, tintColor }) => (
            <Image
              style={{ width: 20, height: 20 }}
              source={
                focused ? Images.icons.leaguesFocused : Images.icons.leagues
              }
            />
          )
        }} />

      <Tab.Screen name="ScoreReporter" component={ScoreReporter}
        navigationOptions={{ 
          title: 'Score Reporter',
          tabBarIcon: ({ focused, horizontal, tintColor }) => (
            <Image
              style={{ width: 30, height: 22 }}
              source={focused ? Images.icons.scoresFocused : Images.icons.scores}
            />
          )
      }}/>

      <Tab.Screen name="Registration" component={
        LoginInfo.LoginInfo.isLoggedIn?
          Registration
          :Login
      }
        navigationOptions={{
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
      }} />
    </Tab.Navigator>
  )
}


const mapStateToProps = state => ({
  LoginInfo: state.Login
})

export default connect(mapStateToProps)(MainNavigator)

