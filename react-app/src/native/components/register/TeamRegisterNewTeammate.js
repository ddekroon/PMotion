import React, { useState} from 'react'
import {View, Picker, Text, Icon} from 'native-base'
import { TextInput } from 'react-native-gesture-handler'
import {StyleSheet} from 'react-native'

export default class AddingTeamMembers extends React.Component {
   
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

                    <View style={styles.floatingBox, {paddingBottom:5, flexDirection:'row', justifyContent: 'center',}}>
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
