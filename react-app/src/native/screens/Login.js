//this is a stub, not meant to be the final product
import React from "react";
import {Text, TextInput, Button, View} from 'react-native'

export default function Login({route, navigation}) {
    return (
        <View>
            <Text style={{fontWeight: 'bold'}}>Login In</Text>
            <Text>User</Text>
            <TextInput/>
            <Text>Pass</Text>
            <TextInput/>
            <Button title={'Check'} onPress={ () => {
                console.log(" = " + JSON.stringify(route.params))
            }}/>

            <Button title={'Login'} onPress={ () => {
                navigation.navigate("PickSport", {registerType:route.params.registerType})
            }}/>
        </View>
    )
}