import React, { useState} from 'react'
import {View, Picker, Text, Icon} from 'native-base'
import { TextInput } from 'react-native-gesture-handler'
import {StyleSheet} from 'react-native'

export default class AddingTeamMembersIndividual extends React.Component {
   
    render() {
        let obj = this.props.json
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
                            onChangeText={ (FN) => {
                                obj.fn = FN
                                this.props.func(obj)
                            }}
                            value={obj.fn}
                            style={styles.FillIn}
                        />
                    </View>

                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Last Name            </Text>
                        <TextInput  //Last name
                            placeholder={'Last Name'}
                            autoCapitalize={'words'}
                            multiline={false}
                            onChangeText={ (LN) => {
                                obj.ln = LN
                                this.props.func(JSON.stringify(obj))
                            }}
                            value={obj.ln}
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
                            onChangeText={ (email) => {
                                obj.email = email
                                this.props.func(JSON.stringify(obj))
                            }}
                            value={obj.email}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Phone                     </Text>
                        <TextInput  //Email
                            placeholder={'Phone'}
                            keyboardType={'phone-pad'}
                            multiline={false}
                            autoComplete={'tel'}
                            onChangeText={ (tel) => {
                                obj.phone = tel
                                this.props.func(JSON.stringify(obj))
                            }}
                            value={obj.phone}
                            style={styles.FillIn}
                        />
                    </View>

                    <View style={styles.floatingBox, {paddingBottom:5, flexDirection:'row', justifyContent: 'space-around'}}>
                        <View>
                            <Text style={styles.text}>Sex</Text>
                            <Picker
                                placeholder='Sex'
                                mode="dropdown"
                                iosIcon={<Icon name="arrow-down" />}
                                style={ styles.picker}
                                selectedValue = {obj.sex}
                                onValueChange={ ((sex) => {
                                    obj.sex = sex
                                    this.props.func(JSON.stringify(obj))
                                }) }
                            >
                                <Picker.Item label="Sex" value='' key={0} />
                                <Picker.Item label="Male" value="Male" key={1} />
                                <Picker.Item label="Female" value="Female" key={2} />
                            </Picker>
                        </View>

                        <View>
                            <Text style={styles.text}>Skill</Text>
                            <Picker
                                placeholder='Skill'
                                mode="dropdown"
                                iosIcon={<Icon name="arrow-down" />}
                                style={ styles.picker}
                                selectedValue = {obj.skill}
                                onValueChange={ ((skill) => {
                                    obj.skill = skill
                                    this.props.func(JSON.stringify(obj))
                                }) }
                            >
                                <Picker.Item label="Skill" value='' key={0} />
                                <Picker.Item label="5 (High)" value="Male" key={1} />
                                <Picker.Item label="4" value="Female" key={2} />
                                <Picker.Item label="3" value="Female" key={3} />
                                <Picker.Item label="2" value="Female" key={4} />
                                <Picker.Item label="1 (Low)" value="Female" key={5} />
                            </Picker>
                        </View>
                    </View>

                </View>
            </View>
        )
    }
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