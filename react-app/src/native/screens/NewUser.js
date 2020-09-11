import React from 'react';
import { View, Text, Image, StyleSheet, TouchableHighlight} from 'react-native';
import { Container, Card, Content, Input, Picker, Item, Form, Label, Icon, Button } from 'native-base';
import Header from '../components/common/Header';
import { Dimensions } from "react-native";
import ValidationHelpers from '../../utils/validationhelpers';
import ToastHelpers from '../../utils/toasthelpers';
import Colors from '../../../native-base-theme/variables/commonColor';


const screenWidth = Math.round(Dimensions.get('window').width);

export default class NewUser extends React.Component {

    constructor(props) {
        super(props)
        this.state = {
            firstName:'',
            lastName:'',
            email:'',
            phone:'',
            gender:'',
            username:'',
            passwordOne:'',
            passwordTwo:'',
            firstNameColor:'#696969',
            lastNameColor:'#696969',
            emailColor:'#696969',
            phoneColor:'#696969',
            genderColor:'#696969',
            userColor:'#696969',
            passwordOneColor:'#696969',
            passwordTwoColor:'#696969',
        }
    }

    basicWordCheck(word) {
        if (word == '') return true;
        if (word.includes('1')) return true;
        if (word.includes('2')) return true;
        if (word.includes('3')) return true;
        if (word.includes('4')) return true;
        if (word.includes('5')) return true;
        if (word.includes('6')) return true;
        if (word.includes('7')) return true;
        if (word.includes('8')) return true;
        if (word.includes('9')) return true;

        return false;
    }

    createAccount() {
        let str = '';
        let submit = true

        if (this.basicWordCheck(this.state.firstName)) {    //Firstname
            str = '- Invalid firstname\n'
            submit = false;
            this.setState({firstNameColor:'red'})
        } else {
            this.setState({firstNameColor:'#696969'})
        }

        if (this.basicWordCheck(this.state.lastName)) {    //lastname
            str += '- Invalid lastname\n'
            submit = false;
            this.setState({lastNameColor:'red'})
        } else {
            this.setState({lastNameColor:'#696969'})
        }

        if ( !ValidationHelpers.isValidEmail(this.state.email)) {    //Email
            str += '- Email is invalid\n'
            submit = false;
            this.setState({emailColor:'red'})
        } else {
            this.setState({emailColor:'#696969'})
        }

        if (!ValidationHelpers.isValidPhoneNumber(this.state.phone)) {  //Phone number
            str += '- Phone number is invalid\n'
            submit = false;
            this.setState({phoneColor:'red'})
        } else {
            this.setState({phoneColor:'#696969'})
        }

        if (this.state.gender == '') {    //gender
            str += '- Please choose a gender\n'
            submit = false;
            this.setState({genderColor:'red'})
        } else {
            this.setState({genderColor:'#696969'})
        }

        if (this.state.username.length < 6) {
            str += '- Username must be at least 6 characters\n'
            submit = false;
            this.setState({userColor:'red'})
        } else {
            this.setState({userColor:'#696969'})
        }

        if (this.state.passwordOne.length < 6) {
            str += '- Password must be at least 6 characters\n'
            submit = false;
            this.setState({passwordOneColor:'red'})

        } else if (this.state.passwordOne != this.state.passwordTwo) {
            str += '- Passwords don\'t match\n'
            submit = false;
            this.setState({passwordOneColor:'red'})
            this.setState({passwordTwoColor:'red'})
        } else {
            this.setState({passwordOneColor:'#696969'})
            this.setState({passwordTwoColor:'#696969'})
        }

        if (submit) {
            //this.props.submitNewUser()
        } else {
            ToastHelpers.showToast(null, str);
        }
    }

    render() {

        return (
        
            <Container>
                <Content>
                    <Card style={{paddingLeft:10}}>
                        
                        <Header
                            title="Create a Profile"
                        />

                        <View style={{alignItems:'center'}}>
                            <Image 
                                source={require("../../images/Perpetualmotionlogo2.png")}
                                style={styles.logo}
                                resizeMode ={'contain'}
                            />
                        </View>
                        
                        <View>
                            <Text style={styles.heading}>Edit Information</Text>
                            <View style={styles.line}/>
                        </View>

                        <View>
                            <Form>
                                <Item>
                                    <Label style={{color:this.state.firstNameColor}}>First Name</Label>
                                    <Input
                                        autoCapitalize="words"
                                        value={this.state.firstName}
                                        onChangeText={elem => this.setState({firstName:elem})}
                                    />
                                </Item>

                                <Item>
                                    <Label style={{color:this.state.lastNameColor}}>last Name</Label>
                                    <Input
                                        autoCapitalize="words"
                                        value={this.state.lastName}
                                        onChangeText={elem => this.setState({lastName:elem})}
                                    />
                                </Item>

                                <Item>
                                    <Label style={{color:this.state.emailColor}}>Email</Label>
                                    <Input
                                        autoCapitalize="none"
                                        value={this.state.email}
                                        onChangeText={elem => this.setState({email:elem})}
                                    />
                                </Item>

                                <Item>
                                    <Label style={{color:this.state.phoneColor}}>Phone Number</Label>
                                    <Input
                                        autoCapitalize="none"
                                        keyboardType="number-pad"
                                        value={this.state.phone}
                                        onChangeText={elem => this.setState({phone:elem})}
                                    />
                                </Item>


                                <Item>
                                    <View style={{flexDirection:'row', alignItems:'space-around'}}>
                                        <Label style={{color:this.state.genderColor}}>Gender</Label>
                                        <View style={{backgroundColor:'red', borderRadius:10}}>
                                            <Picker
                                                note={false}
                                                mode="dropdown"
                                                iosIcon={<Icon name="arrow-down" />}
                                                selectedValue={this.state.gender}
                                                placeholder= {"Gender"}
                                                onValueChange={(val) => {
                                                    this.setState({gender:val})
                                                }}
                                            >
                                                <Picker.Item key={0} label={"Male"} value={"Male"}/>
                                                <Picker.Item key={1} label={"Female"} value={"Female"}/>

                                            </Picker>
                                        </View>
                                    </View>
                                </Item>

                                <Text style={styles.heading}>Login Information</Text>
                                <Text style={styles.subHeading}>Username and password must be between 6 and 16 characters. Please do not use spaces, quotes, or apostrophes.</Text>
                                <View style={styles.line}/>

                                <Item>
                                    <Label style={{color:this.state.userColor}}>Username</Label>
                                    <Input
                                        autoCapitalize="none"
                                        value={this.state.username}
                                        onChangeText={elem => this.setState({username:elem})}
                                    />
                                </Item>

                                <Item>
                                    <Label style={{color:this.state.passwordOneColor}}>Password</Label>
                                    <Input
                                        autoCapitalize="none"
                                        value={this.state.passwordOne}
                                        onChangeText={elem => this.setState({passwordOne:elem})}
                                    />
                                </Item>

                                <Item>
                                    <Label style={{color:this.state.passwordTwoColor}}>Confirm Password</Label>
                                    <Input
                                        autoCapitalize="none"
                                        value={this.state.passwordTwo}
                                        onChangeText={elem => this.setState({passwordTwo:elem})}
                                    />
                                </Item>

                                <View style={{paddingBottom:15}}/>
                                <TouchableHighlight
                                    onPress= { () => {
                                        createAccount()
                                    }}
                                    style={styles.button}
                                >
                                    <Text style={styles.text}>Create Account</Text>
                                </TouchableHighlight>
                                
                            </Form>
                        </View>
                    </Card>
                </Content>
            </Container>
        )
    }
}

const styles = StyleSheet.create({
    heading: {
        fontWeight:'bold',
    },

    subHeading: {
        color: '#474747',
    },
  
    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
    }, 

    logo: {
        width: screenWidth - 100,
    },

    button: {
        width:'80%',
        backgroundColor:Colors.brandSecondary,
        borderRadius: 10,
        flexDirection:'column',
        justifyContent: 'center',
        alignItems:'center',
        height:60,
    },
    
    text: {
        color:'white',
    },

})