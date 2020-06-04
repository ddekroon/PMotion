import React from 'react'
import {View, Text, Button, Picker, Icon  } from 'native-base'
import {StyleSheet} from 'react-native'
import { TextInput } from 'react-native-gesture-handler'

export default class Dash extends React.Component {
    constructor(props) {
        super(props);
        if (this.props.user) {
            this.state = {
                FN:this.props.user.FN,
                LN:this.props.user.LN,
                email:this.props.user.email,
                phone:this.props.user.phone,
            }
        } else {
            this.state = {
                FN:'',
                LN:'',
                email:'',
                phone:'',
            }
        }
        
    }
    
    /** This is the object im expecting to be passed,
     * 
     * const user = {
        user:'imckechn',
        FN:'Ian',
        LN:'McKechnie',
        email:'imckechn@uoguelph.ca',
        phone:'9056915041',
        sex:'Male',
        }

        This is also another item(s) im expecting to be passed to the class:
        -RegsterNewTeam: [true/false]
        -isLoggedIn:[true/false]
     */
    
    render() {
        return (
            <View style={styles.main}>
                <View style={styles.padding}>
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>First Name           </Text>
                        <TextInput  //First name
                            placeholder={'First Name'}
                            multiline={false}
                            value={this.state.FN}
                            autoCapitalize={'words'}
                            autoComplete={'name'}
                            onChangeText={ (FN) => this.setState({ FN })}
                            style={styles.FillIn}
                        />
                    </View>

                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Last Name            </Text>
                        <TextInput  //Last name
                            placeholder={'Last Name'}
                            autoCapitalize={'words'}
                            value={this.state.LN}
                            multiline={false}
                            onChangeText={ (LN) => this.setState({ LN })}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Email                     </Text>
                        <TextInput  //Email
                            placeholder={'Email'}
                            keyboardType={'email-address'}
                            value={this.state.email}
                            multiline={false}
                            autoComplete={'email'}
                            onChangeText={ (email) => this.setState({ email })}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox}>
                        <Text style={styles.text}>Phone Number     </Text>
                        <TextInput  //Phone Number
                            placeholder={'Phone Number'}
                            value={this.state.phone}
                            keyboardType={'number-pad'}
                            multiline={false}
                            onChangeText={ (phone) => this.setState({ phone })}
                            style={styles.FillIn}
                        />
                    </View>
                    
                    <View style={styles.floatingBox, {paddingBottom:5, flexDirection:'column', justifyContent: 'center', alignItems:'center'}}>
                        <Text style={styles.text}>Sex</Text>
                        { this.props.user ? dropDownSex(this.props.user.sex) : dropDownSex(null) }
                    </View>
                </View>
            </View> 
        )
    }
}

//The picker function for chosing the sex of the player
export function dropDownSex(sex){
    console.log("Sex is" + JSON.stringify(sex))
    let chosen = ''
    if (sex) {
        chosen = sex
    } 
    
     
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
                <Picker.Item label="Male" value="Male" key={1} />
                <Picker.Item label="Female" value="Female" key={2} />
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