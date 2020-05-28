import React from 'react'
import {Text, View, StyleSheet, Picker} from 'react-native'
import { TextInput } from 'react-native-gesture-handler'
import {Icon} from 'native-base'

export default class AddingTeamMembers extends React.Component {

    render() {
        this.state = {
            FN:"",
            LN:"",
            email:"",
            phone:""
        }

        return (
            <View style={{flexDirection: 'row'}}>
                <TextInput  //First name
                    placeholder={'First Name'}
                    multiline={false}
                    autoCapitalize={'words'}
                    autoComplete={'name'}
                    onChangeText={text => {this.state.FN = text}}
                    value={this.state.FN}
                    style={styles.FillIn}
                />
                <TextInput  //Last name
                    placeholder={'Last Name'}
                    autoCapitalize={'words'}
                    multiline={false}
                    onChangeText={text => {this.state.LN = text}}
                    value={this.state.LN}
                    style={styles.FillIn}
                />
                <TextInput  //Email
                    placeholder={'Email'}
                    keyboardType={'email-address'}
                    multiline={false}
                    autoComplete={'email'}
                    onChangeText={text => {this.state.email = text}}
                    value={this.state.email}
                    style={styles.FillIn}
                />
                <TextInput  //Phone Number
                    placeholder={'Phone Number'}
                    keyboardType={'number-pad'}
                    multiline={false}
                    onChangeText={text => {this.state.phone = text}}
                    value={this.state.phone}
                    style={styles.FillIn}
                />

                {dropDownSex()}
                
            </View>
        )
    }
    
}

const styles = StyleSheet.create({
    FillIn: {
        color:'red'
    }
})

function dropDownSex(){
    state = {sex: ''}
    updateSex = (sex) => {
        this.setState({ sex: sex})
    }

    <Picker 
        selectedValue={'Sex'} 
        iosIcon={<Icon name="arrow-down" />}
        mode='dropdown'
        placeholder = "Sex"
        selectedValue = {this.state.sex}
          onValueChange = {this.updateSex}
    >
        <Picker.item label="Sex" value="Sex"/>
        <Picker.item label="Male" value="Male"/>
        <Picker.item label="Female" value="Female"/>
    </Picker>
}