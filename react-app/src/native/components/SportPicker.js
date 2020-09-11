import React from "react";
import { View, Image, StyleSheet, TouchableHighlight, Button, Text} from "react-native";
import { Container, Content, Card } from 'native-base'
import Enums from '../../constants/enums'
import Colors from '../../../native-base-theme/variables/commonColor';


export default function PickSport({route, navigation}) {

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
                            <Text style={{color:Colors.brandSecondary}}> sport</Text>
                        </Text>
                        <Text style={styles.subHeading}>Forum will appear after a sport is chosen</Text>
                        <View style={styles.line}/>

                        <TouchableHighlight  onPress={() => {
                            navigate(route, navigation, Enums.sports.Ultimate)
                        }}
                            style={{paddingBottom:25}}
                        >
                            <Image
                                source ={ require('../../images/ultimate-small.png')}
                            />
                        </TouchableHighlight >
                    
                        <TouchableHighlight  onPress={() => {
                            navigate(route, navigation, Enums.sports.Ultimate)
                        }}
                        style={{paddingBottom:25}}
                    >
                            <Image
                                source ={ require('../../images/volleyball-small.png')}
                            />
                        </TouchableHighlight >


                        <TouchableHighlight  onPress={() => {
                            navigate(route, navigation, Enums.sports.Ultimate)                  
                        }}
                        style={{paddingBottom:25}}
                    >
                            <Image
                                source ={ require('../../images/football-small.png')}
                            />
                        </TouchableHighlight >

                        <TouchableHighlight  onPress={() => {
                            navigate(route, navigation, Enums.sports.Ultimate)                       
                        }}
                        style={{paddingBottom:25}}
                    >
                            <Image
                                source ={ require('../../images/soccer-small.png')}
                            />
                        </TouchableHighlight >
                    </View>
                </Card>
            </Content>
        </Container>
    )
}

export function navigate(route, navigation, sport) {

    if (route.params.registerType == 'IndividualRegister') {
        navigation.navigate('IndividualRegister', {sport:sport})

    } else {
        navigation.navigate('RegisterNewTeam', {sport:sport})
    }
}

const styles = StyleSheet.create({ 
    header: {
    fontWeight:'bold',
    fontSize:Colors.fontSizeH1
    },

    subHeading: {
        color: Colors.brandPrimary,
        fontSize:Colors.fontSizeH3,
    },

    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
        paddingBottom:20
    },
})