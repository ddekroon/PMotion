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

const user = {
  user:'imckechn',
  FN:'Ian',
  LN:'McKechnie',
  email:'imckechn@uoguelph.ca',
  phone:'1234567890',
  sex:'Male',
}

export default function Register({ navigation }) {

  return (
    <ScrollView>
      <RegisterTeam sport={1} user={user} />
      
    </ScrollView>
  )
}
