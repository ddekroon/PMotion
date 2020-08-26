import React, { useState} from 'react'
import {View, Picker, Text, Icon} from 'native-base'
import { TextInput } from 'react-native-gesture-handler'
import {StyleSheet} from 'react-native'

let first, last, em, ph, se, sk

export function AddingTeamMembers() {
    /*
    constructor(props) {
        super(props);
        this.state = {
            FN:"",
            LN:"",
            email:"",
            phone:"",
            chosenSex:"",
            chosenSkill:""
        }
    }
    */
   const [FN, setFN] = useState('')
   const [LN, setLN] = useState('')
   const [email, setEmail] = useState('')
   const [phone, setPhone] = useState('')
   const [sex, setSex] = useState('')
   const [skill, setSkill] = useState('')

    first = FN
    last = LN
    em = email
    ph = phone
    se = sex
    sk = skill

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
                        onChangeText={ (FN) => setFN(FN)}
                        value={FN}
                        style={styles.FillIn}
                    />
                </View>

                <View style={styles.floatingBox}>
                    <Text style={styles.text}>Last Name            </Text>
                    <TextInput  //Last name
                        placeholder={'Last Name'}
                        autoCapitalize={'words'}
                        multiline={false}
                        onChangeText={ (LN) => setLN(LN)}
                        value={LN}
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
                        onChangeText={ (email) => setEmail(email)}
                        value={email}
                        style={styles.FillIn}
                    />
                </View>
                
                <View style={styles.floatingBox}>
                    <Text style={styles.text}>Phone Number     </Text>
                    <TextInput  //Phone Number
                        placeholder={'Phone Number'}
                        keyboardType={'number-pad'}
                        multiline={false}
                        onChangeText={ (phone) => setPhone(phone)}
                        value={phone}
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
                            selectedValue = {sex}
                            onValueChange={ (sex) => setSex(sex) }
                        >
                            <Picker.Item label="Sex" value='' key={0} />
                            <Picker.Item label="Male" value="Male" key={1} />
                            <Picker.Item label="Female" value="Female" key={2} />
                        </Picker>
                    </View>

                    <View>
                        <Text style={styles.text}>Skill</Text>
                        <Picker
                            placeholder='Skill Level'
                            mode="dropdown"
                            iosIcon={<Icon name="arrow-down" />}
                            style={ styles.picker}
                            selectedValue = {skill}
                            onValueChange={ (skill) => setSkill(skill) }
                        >
                            <Picker.Item label="Skill" value='' key={0} />
                            <Picker.Item label="5 (High)" value="5" key={5} />
                            <Picker.Item label="4" value="4" key={4} />
                            <Picker.Item label="3" value="3" key={3} />
                            <Picker.Item label="2" value="2" key={2} />
                            <Picker.Item label="1 (Low)" value="1" key={1} />
                        </Picker>
                    </View>
                </View>
            </View>
        </View>
    )
}

export function displayTeamMember() {

    let obj

    obj.image = AddingTeamMembers()
    obj.data = {
        'FN':first,
        'LN':last,
        'email':em,
        'phone':ph,
        'sex':se,
        'skill':sk,
    }

    return obj
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