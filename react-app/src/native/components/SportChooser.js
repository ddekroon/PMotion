import React from "react";
import { View, Image, TouchableHighlight, Button  } from "react-native";

export default function PickSport({route, navigation}) {

    let sport
    return (

        <View>
            <Button title={'Check nav props'} onPress = { () => {
                console.log(" = " + JSON.stringify(route.params))
            }}/>
            <TouchableHighlight  onPress={() => {
                sport = 1
                
                if (route.params.registerType == 'individualRegister') {
                    navigation.navigate('IndividualRegister', {sport:sport})
                    
                } else if (route.params.registerType == 'reregister') {
                    navigation.navigate('Previousleagues', {sport:sport})
                
                } else if (route.params.registerType == 'newTeam') {
                    navigation.navigate('RegisterNewTeam', {sport:sport})
                }
            }}>
                <Image
                    source ={ require('../../images/ultimate-small.png')}
                />
            </TouchableHighlight >

            <TouchableHighlight  onPress={() => {
                sport = 2

                if (route.params.registerType == 'individualRegister') {
                    navigation.navigate('IndividualRegister', {sport:sport})
                
                } else if (route.params.registerType == 'reregister') {
                    navigation.navigate('Previousleagues', {sport:sport})
                
                } else if (route.params.registerType == 'newTeam') {
                    navigation.navigate('RegisterNewTeam', {sport:sport})
                }
            }}>
                <Image
                    source ={ require('../../images/volleyball-small.png')}
                />
            </TouchableHighlight >

            <TouchableHighlight  onPress={() => {
                sport = 3

                if (route.params.registerType == 'individualRegister') {
                    navigation.navigate('IndividualRegister', {sport:sport})
                
                } else if (route.params.registerType == 'reregister') {
                    navigation.navigate('Previousleagues', {sport:sport})
                
                } else if (route.params.registerType == 'newTeam') {
                    navigation.navigate('RegisterNewTeam', {sport:sport})
                }
            }}>
                <Image
                    source ={ require('../../images/football-small.png')}
                />
            </TouchableHighlight >

            <TouchableHighlight  onPress={() => {
                sport = 4

                if (route.params.registerType == 'individualRegister') {
                    navigation.navigate('IndividualRegister', {sport:sport})
                
                } else if (route.params.registerType == 'reregister') {
                    navigation.navigate('Previousleagues', {sport:sport})
                
                } else if (route.params.registerType == 'newTeam') {
                    navigation.navigate('RegisterNewTeam', {sport:sport})
                }
            }}>
                <Image
                    source ={ require('../../images/soccer-small.png')}
                />
            </TouchableHighlight >

        </View>
    )
}