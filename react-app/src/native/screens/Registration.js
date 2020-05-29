import React from 'react'
import {Text, Button, View, TextInput } from 'react-native'
import PickTeam from '../components/register/Teams'
import PickLeagues from '../components/register/ChooseLeague'
import AddingTeamMembers from '../components/register/TeamMember'

const Ultimate = {
  "name":"Ultimate",
  "id":1
}

const Volleyball = {
  "name":"Volleyball",
  "id":2
}

const Football = {
  "name":"Football",
  "id":3
}

const Soccer = {
  "name":"Soccer",
  "id":4
}

export default class Registration extends React.Component {
  
  render() {
    
    {this.props.sports = {}}
    return (
      <View>
        <PickTeam sports={ [Ultimate, Football, Volleyball, Soccer]   }/>     
        
        <Text>Prefered League</Text>   
        {/*<PickLeagues sport={4}/>*/}

        <Text>------------------</Text>   
        <AddingTeamMembers/>
        <Text>------------------</Text>   
        
      </View>
    )
  }
}

  