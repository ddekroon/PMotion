import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Image } from 'react-native'

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
    
    <Tab.Navigator {...NavigationProps.bottomTabConfig} screenOptions={({route}) => ({
      tabBarIcon: ({ focused, color, size }) => {
        let source, style;

        if (route.name === 'Leagues') {
          source = focused
            ? Images.icons.leaguesFocused
            : Images.icons.leagues;
          style = { width: 20, height: 20 }
        } else if (route.name === 'Score Reporter') {
          source = focused 
            ? Images.icons.scoresFocused
            : Images.icons.scores;
          style = { width: 30, height: 22 }
        } else {
          source = focused 
            ? Images.icons.registrationFocused
            : Images.icons.registration;
          style = { width: 20, height: 20 }
        }

        // You can return any component that you like here!
        return <Image
          style={style}
          source={source}
        />;
      }
    })}>
      <Tab.Screen name="Leagues" component={SportsNavigator} />
      <Tab.Screen name="Score Reporter" component={ScoreReporter} />
      <Tab.Screen name="Registration" component={LoginInfo.LoginInfo.isLoggedIn? Registration :Login} />
    </Tab.Navigator>
  )
}


const mapStateToProps = state => ({
  LoginInfo: state.Login
})

export default connect(mapStateToProps)(MainNavigator)

