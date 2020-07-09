import React from 'react'
import {Text, Button, View, TextInput, Header, ScrollView, StyleSheet, Modal, TouchableHighlight } from 'react-native'
import PickTeam from '../components/register/Teams'
import PickLeagues from '../components/register/ChooseLeague'
import AddingTeamMembers from '../components/register/TeamMember'
import Comment from '../components/register/Comment'
//import RegisterTeam from '../components/register/RegisterTeam'
import Login from './todo/Login'
import Navigation from '../constants/navigation'
import PreviousLeagues from './PreviousLeagues'
import RegisterTeam from './RegisterTeam'
import ChooseLeague from '../components/register/ChooseLeague'
import IndividualRegister from './IndividualRegister'

/*
const me = {
  id:'imckechn',
  FN:'Ian',
  LN:'McKechnie',
  email:'imckechn@uoguelph.ca',
  phone:'1234567890',
  sex:'Male',
}*/

export default function Register({ navigation}) {
  let me = {
    id:'imckechn',
    FN:'Ian',
    LN:'McKechnie',
    email:'imckechn@uoguelph.ca',
    phone:'1234567890',
    sex:'Male',
  }

  return (
    <ScrollView>
      <Text>Perpetual Motion Registration</Text>
      <Button title={'Register as an Indivdual or Small Group'} onPress={ () => {
        navigation.navigate('PickSport', {registerType:'individualRegister'})
      }}/>

      <Button title={'Re-register Previous Team'} onPress={ () => {
        navigation.navigate('Login', {registerType:'reregister'})
      }}/>

      <Button title={'Register New Team'} onPress={ () => {
        navigation.navigate('Login', {registerType:'newTeam'})
      }}/>

      <Button title={'View Old leagues'} onPress={ () => {
        navigation.navigate('Previousleagues')
      }}/>
      
    </ScrollView>
  )
}