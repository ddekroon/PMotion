import React from 'react';
import {createAppContainer, createMaterialTopTabNavigator} from 'react-navigation';

import FootballScreen from '../components/Football';
import UltimateScreen from '../components/Ultimate';
import SoccerScreen from '../components/Soccer';
import VolleyballScreen from '../components/Volleyball';
import { Container } from 'native-base';

/**
 * {lookups.sports.map((item, key)=>(
         <Text key={key}> { item.name } </Text>)
         )}
 */

const appNavigator = createMaterialTopTabNavigator (
  {
    Ultimate: UltimateScreen,
    VolleyBall: VolleyballScreen,
    Soccer: SoccerScreen,
    Football: FootballScreen,
  },

  {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: 'gray',
      style: {
        backgroundColor: '#303030'
      },
      indicatorStyle: {
        borderBottomColor: 'red',
        borderBottomWidth: 3,
      },
      labelStyle: {
        fontSize: 9 
      },
    }
  },
);

const AppIndex = createAppContainer(appNavigator);

export default class Leagues extends React.Component {
  render(){
    return (
      <AppIndex/> 
    );
  }
}   
