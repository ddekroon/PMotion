import React, { useState} from 'react'
import {View, Picker, Text, Icon} from 'native-base'
import { TextInput } from 'react-native-gesture-handler'
import {StyleSheet} from 'react-native'

export default class AddingTeamMembers extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            FN:"",
            LN:"",
            email:"",
            phone:"",
        }
    }
    
    render() {    
        return (
            <View style={styles.main}>
                <View style={styles.padding}>
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>First Name           </Text>
                        <TextInput  //First name
                            placeholder={'First Name'}
                            multiline={false}
                            autoCapitalize={'words'}
                            autoComplete={'name'}
                            onChangeText={ (FN) => this.setState({ FN })}
                            value={this.state.FN}
                            style={styles.FillIn}
                        />
                    </View>

                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Last Name            </Text>
                        <TextInput  //Last name
                            placeholder={'Last Name'}
                            autoCapitalize={'words'}
                            multiline={false}
                            onChangeText={ (LN) => this.setState({ LN })}
                            value={this.state.LN}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Email                     </Text>
                        <TextInput  //Email
                            placeholder={'Email'}
                            keyboardType={'email-address'}
                            multiline={false}
                            autoComplete={'email'}
                            onChangeText={ (email) => this.setState({ email })}
                            value={this.state.email}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Phone Number     </Text>
                        <TextInput  //Phone Number
                            placeholder={'Phone Number'}
                            keyboardType={'number-pad'}
                            multiline={false}
                            onChangeText={ (phone) => this.setState({ phone })}
                            value={this.state.phone}
                            style={styles.FillIn}
                        />
                    </View>

                    <View style={styles.floatingBox, {paddingBottom:5, flexDirection:'row', justifyContent: 'center',}}>
                        <View>
                            <Text style={styles.text}>Sex             </Text>
                            {dropDownSex()}
                        </View>

                        <View>
                            <Text style={styles.text}>Skill             </Text>
                            {dropDownSkill()}
                        </View>
                    </View>
                    
                    
                </View>
            </View>
            
        )
    }
    
}

//The picker function for chosing the sex of the player
export function dropDownSex(){
    let chosen = ''
     
    return (
        <View>
            <Picker
                placeholder='Sex'
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={ styles.picker}
                selectedValue = {chosen}
                onValueChange={ (sex) => {chosen = sex, console.log("chosen = " + chosen)} }
            >
                <Picker.Item label="Sex" value='' key={0} />
                <Picker.Item label="Male" value="Male" key={1} />
                <Picker.Item label="Female" value="Female" key={2} />
            </Picker>
        </View>
    );
}

//The picker function for chosing the skill level of the player
export function dropDownSkill(){
    let chosen = ''
     
    return (
        <View>
            <Picker
                placeholder='Skill Level'
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={ styles.picker}
                selectedValue = {chosen}
                onValueChange={ (skill) => {chosen = skill} }
            >
                <Picker.Item label="Skill" value='' key={0} />
                <Picker.Item label="5 (High)" value="5" key={5} />
                <Picker.Item label="4" value="4" key={4} />
                <Picker.Item label="3" value="3" key={3} />
                <Picker.Item label="2" value="2" key={2} />
                <Picker.Item label="1 (Low)" value="1" key={1} />
            </Picker>
        </View>
    );
}


const styles = StyleSheet.create({
    FillIn: {
        color:'black',
        borderBottomColor:'red',
        borderBottomWidth: StyleSheet.hairlineWidth,
        width:200,
        fontSize:20
    },

    text: {
        fontSize:20,
        flex:1,
        flexDirection:'column',
        alignItems:'center',
    },

    floatingBox: {
        flexDirection:'row',
        height:40,
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center'
    },

    picker: {
        borderWidth:1,
        justifyContent: 'flex-start',
        width:100
    },

    main: {
        backgroundColor:'white',
        borderRadius:15,
    },

    padding: {
        padding:10
    }
})