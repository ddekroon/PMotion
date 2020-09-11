import React, { useState} from 'react'
import {View, Text, Button, Picker, Icon  } from 'native-base'
import {StyleSheet} from 'react-native'
import { TextInput } from 'react-native-gesture-handler'
import Colors from '../../../native-base-theme/variables/commonColor';

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
                        <Text style={styles.text}>Gender</Text>
                        { this.props.user ? dropDownGender(this.props.user.gender) : dropDownGender(null) }
                    </View>
                </View>
            </View> 
        )
    }
}

//const [chosen, setChosen] = useState()

//The picker function for chosing the gender of the player
export const dropDownGender = (gender) => {
    let chosen = ''

    if (gender) {
        chosen = gender
    } 
     
    return (
        <View>
            <Picker
                placeholder='Gender'
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={ styles.picker}
                selectedValue = {chosen}
                onValueChange={ (gender) => chosen = gender }
            >
                <Picker.Item label='Gender' value='Gender' key={0} />
                <Picker.Item label="Male" value="Male" key={1} />
                <Picker.Item label="Female" value="Female" key={2} />
            </Picker>
        </View>
    );
}

const styles = StyleSheet.create({
    FillIn: {
        color:'black',
        borderBottomColor:Colors.brandSecondary,
        borderBottomWidth: StyleSheet.hairlineWidth,
        width:200,
        fontSize:20
    },

    text: {
        fontSize:Colors.fontSizeH3,
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