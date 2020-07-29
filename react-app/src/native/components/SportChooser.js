import React from "react";
import { View, Image, StyleSheet, TouchableHighlight, Button, Text} from "react-native";
import { Container, Content, Card } from 'native-base'

export default function PickSport({route, navigation}) {

    let sport

    return (
        <Container>
            <Content>
                <Card>
                    <View style={{
                        flex: 1,
                        flexDirection: 'column',
                        justifyContent: 'space-between',
                        alignItems:'center'
                    }}>
                        <Text style={styles.header}>Choose a 
                            <Text style={{color:'red'}}> sport</Text>
                        </Text>
                        <Text style={styles.subHeading}>Forum will appear after a sport is chosen</Text>
                        <View style={styles.line}/>
                        <View style={{paddingBottom:20}}/>

                        <Card>
                            <TouchableHighlight  onPress={() => {
                                sport = 1
                                
                                if (route?.params?.registerType == 'individualRegister') {
                                    navigation.navigate('IndividualRegister', {sport:sport})
                                    
                                } else if (route?.params?.registerType == 'reregister') {
                                    navigation.navigate('Previousleagues', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'newTeam') {
                                    navigation.navigate('RegisterNewTeam', {sport:sport})
                                
                                } else {
                                    console.log("Big move")
                                }
                            }}>
                                <Image
                                    source ={ require('../../images/ultimate-small.png')}
                                />
                            </TouchableHighlight >
                        </Card>
                        
                        <Card>
                            <TouchableHighlight  onPress={() => {
                                sport = 2

                                if (route?.params?.registerType == 'individualRegister') {
                                    navigation.navigate('IndividualRegister', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'reregister') {
                                    navigation.navigate('Previousleagues', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'newTeam') {
                                    navigation.navigate('RegisterNewTeam', {sport:sport})
                                } else {
                                    navigation.navigate('RegisterNewTeam')  //Runs when the user selects it from the registration page
                                }
                            }}>
                                <Image
                                    source ={ require('../../images/volleyball-small.png')}
                                />
                            </TouchableHighlight >

                        </Card>

                        <Card>
                            <TouchableHighlight  onPress={() => {
                                sport = 3

                                if (route?.params?.registerType == 'individualRegister') {
                                    navigation.navigate('IndividualRegister', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'reregister') {
                                    navigation.navigate('Previousleagues', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'newTeam') {
                                    navigation.navigate('RegisterNewTeam', {sport:sport})
                                } else {
                                    navigation.navigate('RegisterNewTeam')  //Runs when the user selects it from the registration page
                                }
                            }}>
                                <Image
                                    source ={ require('../../images/football-small.png')}
                                />
                            </TouchableHighlight >
                        </Card>

                        <Card>
                            <TouchableHighlight  onPress={() => {
                                sport = 4

                                if (route?.params?.registerType == 'individualRegister') {
                                    navigation.navigate('IndividualRegister', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'reregister') {
                                    navigation.navigate('Previousleagues', {sport:sport})
                                
                                } else if (route?.params?.registerType == 'newTeam') {
                                    navigation.navigate('RegisterNewTeam', {sport:sport})
                                } else {
                                    navigation.navigate('RegisterNewTeam')  //Runs when the user selects it from the registration page
                                }
                            }}>
                                <Image
                                    source ={ require('../../images/soccer-small.png')}
                                />
                            </TouchableHighlight >
                        </Card>
                    </View>
                </Card>
            </Content>
        </Container>
    )
}

const styles = StyleSheet.create({ 
    header: {
    fontWeight:'bold',
    fontSize:35
    },

    subHeading: {
        color: '#474747',
        fontSize:12,
    },

    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
    },
})