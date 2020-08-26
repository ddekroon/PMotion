import React from 'react'
import {Text, View, ScrollView, StyleSheet, Modal, TouchableHighlight, Button } from 'react-native'
import PickTeam from '../components/register/Teams'
import PickLeagues from '../components/register/LeaguePicker'
import AddingTeamMembers from '../components/register/TeamMember'
import Comment from '../components/register/Comment'

//This is the base template im using for team register
const user = {
    user:'imckechn',
    FN:'Ian',
    LN:'McKechnie',
    email:'imckechn@uoguelph.ca',
    phone:'9056915041',
    sex:'Male',
}

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

export default class IndividualRegister extends React.Component {
    state = {
        modalVisible: false,
        players:[<AddingTeamMembers key={0}/>]
    };

    setModalVisible = (visible) => {
        this.setState({ modalVisible: visible });
    }

    render() {
        const addPlayer = () => {
            let arr = this.state.players
            arr.push(<AddingTeamMembers key={arr.length}/>)
    
            this.setState({players: arr})
        }

        const { modalVisible } = this.state

        console.log("More here than before")
        
        return(
            <ScrollView>
                <Text style={styles.header}>Registration</Text>
                <View style={{alignItems:'center', justifyContent:'center'}}>
                    <PickTeam sports={ [Ultimate, Football, Volleyball, Soccer]}/>
                    <Modal
                        animationType="slide"
                        transparent={true}
                        visible={modalVisible}
                    >
                    <View style={styles.centeredView}>
                        <View style={styles.modalView}>
                            <ScrollView>
                                <Text>
                                    <Text style={{fontWeight:'bold'}}>A:</Text>
                                    <Text>This 7 vs 7 division is recommended for teams and players who would like to play very competitive Ultimate at a high-pace. Players generally have lots of tournament experience and a very strong knowledge of rules and strategies.</Text>
                                </Text>

                                <Text>
                                    <Text style={{fontWeight:'bold'}}>B7:</Text>
                                    <Text>This 7 vs 7 division is recommended for teams and players who would like to try playing 7s Ultimate. Players generally have at least a couple years of league experience and are fairly knowledgeable of rules and strategies. </Text>
                                </Text>

                                <Text>
                                    <Text style={{fontWeight:'bold'}}>B/B1:</Text>
                                    <Text>This 5 vs 5 division is recommended for teams and players who are of high intermediate skill level. Players generally have a few years of league experience, and a good knowledge of rules and strategies, such as the stack. </Text>
                                </Text>

                                <Text>
                                    <Text style={{fontWeight:'bold'}}>B2:</Text>
                                    <Text>This 5 vs 5 division is recommended for teams and players who are of intermediate skill level. Players generally have a couple years of league experience and a decent knowledge of rules and strategies, such as the "stack". </Text>
                                </Text>

                                <Text>
                                    <Text style={{fontWeight:'bold'}}>C/C1:</Text>
                                    <Text>This 5 vs 5 division is recommended for teams and players who are of high beginner skill levels. Players generally have at least a year of league experience and a basic knowledge of rules and strategies. </Text>
                                </Text>

                                <Text>
                                    <Text style={{fontWeight:'bold'}}>C2:</Text>
                                    <Text>This 5 vs 5 division is recommended for teams and players who are new to the sport of ultimate. Players have less than a year of league experience and have little knowledge of rules and strategies. Players are more focused on learning the game and are less concerned with the skill level.</Text>
                                </Text>
                            </ScrollView>
                            <TouchableHighlight
                            style={{ ...styles.openButton, backgroundColor: "red", paddingTop:10 }}
                            onPress={() => {
                                this.setModalVisible(!modalVisible);
                            }}
                            >
                                <Text style={styles.textStyle}>Close</Text>
                            </TouchableHighlight>
                        </View>
                    </View>
                </Modal>

                <TouchableHighlight
                    style={styles.openButton}
                    onPress={() => {
                    this.setModalVisible(true);
                    }}
                >
                    <Text style={styles.textStyle}>Show League Info</Text>
                </TouchableHighlight>
                    
                <Text style={styles.normalText}>Prefered League
                    <Text style = {styles.normalText, {color:'red'}}>*</Text>
                </Text>   
                <PickLeagues sport={4} style={{paddingBottom:10}}/>

                <Text style={styles.normalText}>Secondary Choice</Text>   
                <PickLeagues sport={4} style={{paddingBottom:10}}/>

                <Text style={styles.normalText}>Tertiary Choice</Text>   
                <PickLeagues sport={4} style={{paddingBottom:10}}/>
            </View>
                
            <View style={{padding:10}}>
                {this.state.players}
                <Button title={'Add Player'} onPress={addPlayer}/>
            </View>
            <Comment/>
            </ScrollView>
        )
    }
}

const styles = StyleSheet.create({
    textStyle: {
      fontSize:20,
    },
  
    centeredView: {
      flex: 1,
      justifyContent: "center",
      alignItems: "center",
      marginTop: 22
    },
    modalView: {
      margin: 20,
      backgroundColor: "white",
      borderRadius: 20,
      padding: 35,
      alignItems: "center",
      shadowColor: "#000",
      shadowOffset: {
        width: 0,
        height: 2
      },
      shadowOpacity: 0.25,
      shadowRadius: 3.84,
      elevation: 5
    },
    openButton: {
      backgroundColor: "red",
      borderRadius: 20,
      padding: 10,
      elevation: 2,
      width:'40%'
    },
    textStyle: {
      color: "white",
      fontWeight: "bold",
      textAlign: "center"
    },
    modalText: {
      marginBottom: 15,
      textAlign: "center"
    },
  
    header: {
      fontSize:20,
      textAlign:'center',
      paddingVertical:10,
      fontWeight:'bold',
    
    },
  
    normalText: {
      fontSize:20,
    },
  });