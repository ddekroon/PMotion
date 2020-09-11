//Doesn't need anything passed to it, but somehow needs to know the users previous teams

import React, { useState} from 'react'
import {Text, Picker, Icon, Container, Content, Card} from 'native-base'
import {Button, View, Image} from 'react-native'
import Colors from '../../../native-base-theme/variables/commonColor'
import {StyleSheet} from 'react-native'
import { TextInput, ScrollView, TouchableHighlight } from 'react-native-gesture-handler'
import * as previousTeamHelpers from '../../utils/previousTeamsHelpers'
import store from '../../store/teams'
import HeaderBackButton from '@react-navigation/stack'

var ROW_SIZE = 30
var CHECKER = 0

//The colour part of this is still broken ;/
export default function Previousleagues({navigation, route}) {

    /*HeaderBackButton.onPress(() => {
        navigation.goHome()
    })*/

    var oldLeagues = store?.teams?store.teams:[];    //All the teams from the store are now in the oldLeagues var
    var searchedLeague = [];

    if (oldLeagues.length != 0) {
        oldLeagues = [];
    }
    
    // ******** <Testers to see if if it works with teams> **
    previousTeamHelpers.addLeague(oldLeagues, 'name1', 'league1', 'season1', 'No', route)      //1
    previousTeamHelpers.addLeague(oldLeagues, 'name2', 'league2', 'season2', 'No', route)      //2
    previousTeamHelpers.addLeague(oldLeagues, 'name3', 'league3', 'season3', 'No', route)      //3
    previousTeamHelpers.addLeague(oldLeagues, 'name4', 'league4', 'season4', 'Yes', route)     //4
    previousTeamHelpers.addLeague(oldLeagues, 'name5', 'league5', 'season5', 'No', route)      //5
    previousTeamHelpers.addLeague(oldLeagues, 'name6', 'league6', 'season6', 'Yes', route)     //6
    previousTeamHelpers.addLeague(oldLeagues, 'name7', 'league7', 'season7', 'Yes', route)     //7
    previousTeamHelpers.addLeague(oldLeagues, 'name8', 'league8', 'season8', 'No', route)      //8
    previousTeamHelpers.addLeague(oldLeagues, 'name9', 'league9', 'season9', 'Yes', route)     //9
    previousTeamHelpers.addLeague(oldLeagues, 'name10', 'league10', 'season10', 'No', route)   //10
    previousTeamHelpers.addLeague(oldLeagues, 'name11', 'league11', 'season11', 'Yes', route)  //11
    previousTeamHelpers.addLeague(oldLeagues, 'name12', 'league12', 'season12', 'Yes', route)  //12
    previousTeamHelpers.addLeague(oldLeagues, 'name13', 'league13', 'season13', 'Yes', route)  //13
    // ******** </Testers to see if if it works with teams> **

    const [chosen, setChosen] = useState(10)
    const [pageNum, setPageNum] = useState(1)   //Starts at one instead of zero because this is what prints at the bottom of the card that the user will see
    const [beingShown, setBeingShown] = useState(oldLeagues)

    return (
        <Container>
            <Content>
                <Card style={{paddingLeft:10}}>
                    <ScrollView>
                        {/**Header */}
                        <Text style={styles.header}>Teams Previously Registered</Text>
                        <Text style={styles.subheading}>Click on your team's name to re-register a previous team for the upcoming season</Text>
                        <View style={{paddingBottom:5}}/>
                        <View style={styles.line}/>
                        <View style={{paddingBottom:15}}/>

                        {/*How many elements being shown on the page */}
                        <View style={styles.splitEnds}>
                            <View style={styles.sideBySide}>
                                <Text>Show</Text>
                                <Picker
                                    placeholder='10'
                                    mode="dropdown"
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.picker}
                                    selectedValue = {chosen}
                                    onValueChange={ (amount) => {
                                        //Set the chosen amount per page and reset the page number to the first page
                                        setChosen(amount), 
                                        setPageNum(1)
                                    } }
                                >
                                    <Picker.Item label="10" value="10" key={1}/>
                                    <Picker.Item label="25" value="25" key={2}/>
                                    <Picker.Item label="50" value="50" key={3}/>
                                    <Picker.Item label="100" value="100" key={4}/>
                                </Picker>

                                <Text>entries</Text>
                            </View>

                            <View style={styles.sideBySide}>
                                {/* Allow the user to search through all the previous teams */}
                                <Text>Search</Text>
                                <TextInput 
                                    style={styles.search}
                                    onChangeText={text => { 

                                        searchedLeague = previousTeamHelpers.updateSearch(text, oldLeagues)
                                        setPageNum(1)

                                        if (text.length != 0) {
                                            setBeingShown(searchedLeague)
                                            
                                        } else {
                                            setBeingShown(oldLeagues)
                                        }
                                    }}
                                />
                            </View>
                        </View>

                        <View style={{paddingBottom:10}}/>
                        <View style={{flexDirection:'row', width:'95%'}}>
                            {/* Headers for all the leagues being shown */}
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>Name</Text>
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>League</Text>
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>Season</Text>
                            
                            {/*Made the view clickable so you can sort by if they have been registered */}
                            <TouchableHighlight onPress={() => {
                                //This makes it so the user can keep clicking and getting different organizations each time (yes first, no first, and original order)
                                if (CHECKER == 0 ) {
                                    setBeingShown(previousTeamHelpers.organizeList(beingShown))
                                    CHECKER = 1   // The reason this is here is because  when I use 'if (beingShown == organizeList(oldLeagues))' it always returns false and I don't get it                

                                } else if (CHECKER == 1) {
                                    CHECKER = 2
                                    setBeingShown(previousTeamHelpers.organizeListReverse(beingShown))

                                } else {
                                    CHECKER = 0
                                    setBeingShown(previousTeamHelpers.setListToDefault(beingShown, oldLeagues))
                                }

                                setPageNum(1)
                            }}>
                                <View style={{flexDirection:'row', alignItems:'left', justifyContent:'left'}}>
                                    <Image
                                        source={require('../../images/icons/list.png')}
                                    />
                                    <Text style={styles.first, {fontWeight:'bold'}}>Registered</Text>
                                </View>
                            </TouchableHighlight>
                        </View>

                        <View style={{ height:( beingShown.length > 0 && chosen < beingShown.length? chosen*ROW_SIZE: beingShown.length*ROW_SIZE), borderWidth:3, overflow:'hidden'}}>
                            {/** The expression for height shows either the chosen amount out of the total group, or if the chosen amount is bigger than the group,  just the full group */}
                            {
                                count = -1,
                                beingShown.map( (elem) => {
                                    if (beingShown.indexOf(elem) >= (pageNum-1)*(chosen) && beingShown.indexOf(elem) < (pageNum) * (chosen)) {
                                        count++;
                                        if (elem.nav) {
                                            return (
                                                <View style={{height:ROW_SIZE, justifyContent: 'space-between', flexDirection:'row', backgroundColor:previousTeamHelpers.getAlternatingColours(count)}} key={elem.key} >
                                                    <TouchableHighlight onPress={() => {
                                                        navigation.navigate(elem.navigationLocation, {
                                                            team: {
                                                                name:elem.teamName,
                                                                league:elem.league,
                                                            },
                                                            sport:elem.sport,
                                                            //More vals will get past once I make server requests
                                                        })
                                                    }}>
                                                        <Text style={{fontSize:20, textAlign:'center', color:Colors.brandSecondary, paddingLeft:10}}>{elem.teamName}</Text>
                                                </TouchableHighlight>

                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.league}</Text>
                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.season}</Text>
                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.isRegistered}</Text>
                                                </View>
                                            )
                                        } else {
                                            return (
                                                <View style={{height:ROW_SIZE, justifyContent: 'space-between', flexDirection:'row', backgroundColor:previousTeamHelpers.getAlternatingColours(count)}} key={elem.key} >
                                                    <Text style={{fontSize:20, textAlign:'center', color:Colors.brandPrimary, paddingLeft:10}}>{elem.teamName}</Text>
                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.league}</Text>
                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.season}</Text>
                                                    <Text style={{fontSize:20, textAlign:'center', width:'25%', color:Colors.brandPrimary}}>{elem.isRegistered}</Text>
                                                </View>
                                            )
                                        }
                                    }
                                })
                            }

                        </View>

                        <View style={{paddingBottom:10}}/>
                        <View>
                            <View style={{ 
                                opacity: Math.ceil(beingShown.length/chosen) > 1 ? 100: 0,
                                flexDirection:'row',
                                justifyContent: 'center',
                                alignItems: 'center',
                            }}>

                                {/* the bottoms at the bottom for chosing the page number and go forwards/backwards a page*/}
                                <Button title={'Previous'} color={ (pageNum == 1? 'grey' : 'red') } onPress={ () => setPageNum(previousTeamHelpers.previousPress(pageNum)) }/>
                                <Button title={ '' + previousTeamHelpers.getFirst(pageNum, Math.ceil(beingShown.length/chosen)) } color={ (pageNum == 1? 'grey' : 'red') } onPress={() => setPageNum(previousTeamHelpers.firstPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                                
                                <View style = { Math.ceil(beingShown.length/chosen) > 1 ? styles.show : styles.hide}>

                                    <Button 
                                        title={ '' + previousTeamHelpers.getSecond(pageNum, Math.ceil(beingShown.length/chosen)) } 
                                        color={ (pageNum > 1 && pageNum < Math.ceil(beingShown.length/chosen) - 1) || pageNum == 2 ? 'grey' : 'red' } 
                                        onPress={() => setPageNum(previousTeamHelpers.secondPress(pageNum, Math.ceil(beingShown.length/chosen))) }
                                    />
                                </View>

                                <View style = { Math.ceil(beingShown.length/chosen) > 2 ? styles.show : styles.hide}>

                                    <Button 
                                        title={ '' + previousTeamHelpers.getThird(pageNum, Math.ceil(beingShown.length/chosen)) } 
                                        color={ (previousTeamHelpers.thirdIsGrey(pageNum, Math.ceil(beingShown.length/chosen)) ? 'grey' : 'red') } 
                                        onPress={() => setPageNum(previousTeamHelpers.thirdPress(pageNum, Math.ceil(beingShown.length/chosen))) }
                                    />
                                </View>

                                <View style = { Math.ceil(beingShown.length/chosen) > 3 ? styles.show : styles.hide}>
                                    
                                    <Button 
                                        title={ '' + previousTeamHelpers.getFourth(pageNum, Math.ceil(beingShown.length/chosen)) } 
                                        color={ (pageNum >= Math.ceil(beingShown.length/chosen)? 'grey' : 'red') } 
                                        onPress={() => setPageNum(previousTeamHelpers.fourthPress(pageNum, Math.ceil(beingShown.length/chosen))) }
                                    />
                                </View>

                                <Button title={'Next'} color={ (pageNum >= Math.ceil(beingShown.length/chosen)? 'grey' : 'red') } onPress={() => setPageNum(previousTeamHelpers.nextPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                            </View>
                        </View>

                        {/**Show the page number */}
                        <View>
                            <Text style={styles.subheading}>Page {pageNum} of {Math.ceil(beingShown.length/chosen)} entries.</Text>
                        </View>
                    </ScrollView>
                </Card>
            </Content>
        </Container>
    )
}

//Styles
const styles = StyleSheet.create ({
    elem: {
        height:ROW_SIZE,
        justifyContent: 'space-between',
        borderWidth:1,
    },

    first: {
        textAlign:'center',
        width:'25%'
    },

    header: {
        fontWeight:'bold',
    },

    subHeading: {
        color: '#474747',
    },

    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
    },

    sideBySide: {
        flexDirection:'row',
        justifyContent: 'center',
        alignItems: 'center',
    },

    splitEnds: {
        flexDirection:'row',
        justifyContent:'space-between',
    },

    search: {
        borderBottomWidth:1,
        borderBottomColor:'red',
        width:120,
    },

    picker: {
        width:100,
        borderWidth:1,
    },

    show: {
        opacity:100
    },

    hide: {
        opacity:0
    },
    
})