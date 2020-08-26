//this is a stub, not meant to be the final product
import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import {
  Container, Content, Form, Item, Label, Input, Text, Button, View, Card,
} from 'native-base';
import Header from '../components/common/Header';
import Spacer from '../components/common/Spacer';
import { setLoggedIn, getLoginInfo, logOut } from '../../actions/login';
import ToastHelpers from '../../utils/toasthelpers'
import { Image, StyleSheet, TouchableHighlight } from 'react-native';
import {Dimensions } from "react-native";

const screenWidth = Math.round(Dimensions.get('window').width);

class Login extends React.Component{

  static propTypes = {
    setLoggedIn: PropTypes.func.isRequired,
    getLoginInfo: PropTypes.func.isRequired,
    logOut: PropTypes.func.isRequired,
  }
    
  constructor(props) {
    super(props)
    this.state = {
      username:'',
      password:''
    }
  }
    
  render() {

    let user = {  //Stub
      id:1234,
      FN:'Ian',
      LN:'McKechnie',
      email:'imckechn@uoguelph.ca',
      phone:'1234567890',
      sex:'Male',
    }

    let login = this.props.loggedIn.Login

    if (login.isLoggedIn == true) {
      console.log("Already logged in")
      if (this.props.route.params.registerType == 'reregister') {
        this.props.navigation.navigate("Previousleagues", {use:'reregister'})
      
      } else if (this.props.route.params.registerType == 'Profile') {
        this.props.navigation.navigate("profile", {user:user})  //User will be from the server
      
      } else {
        this.props.navigation.navigate("PickSport", {registerType:this.props.route.params.registerType})
      }
    }

    return (
      <Container>
        <Content>
          
            <Card style={{height:'200%'}}>
              <Header
                title="Welcome back"
                content="Please use your username and password to login."
              />
              <View style={styles.imageView}>
                <Image 
                  source={require('../../images/PerpetualMotionLeaf_square.png')}
                  style={styles.logo}
                  resizeMode ={'contain'}
                />
                
              </View>

              <Form>
                <Item>
                  <Label>Username</Label>
                  <Input
                    autoCapitalize="none"
                    value={this.state.username}
                    onChangeText={username => this.setState({username:username})}
                  />

                </Item>

                <Item>
                  <Label>Password</Label>
                  <Input
                    secureTextEntry
                    value={this.state.password}
                    onChangeText={pass => this.setState({password:pass})}
                  />

                  <Spacer size={20} />
                </Item>
              </Form>

              <View padder>
                <Button block title={'Login'} onPress={() => {
                  
                  let item = filler(this.state.username, this.state.password);

                  if (item === false) {
                    ToastHelpers.showToast(null, "Invalid Username Password Combo");
                    //Propbably put the return from the log in checker when actually putting it in.
                  
                  } else {
                    this.props.setLoggedIn(item)
                    
                    if (this.props.route.params.registerType == 'reregister') {
                      this.props.navigation.navigate("Previousleagues", {use:'reregister'})
                    } else {
                      this.props.navigation.navigate("PickSport", {registerType:this.props.route.params.registerType})
                    }
                  }
                }}>
                  <Text>Login</Text>
                </Button>
              </View>

              <View padder>
                <Button block title={'Temp button'} onPress={() => {
                  this.props.logOut()
                }}>
                  <Text>Logout (Temp button)</Text>
                </Button>
              </View>

              <View style={{flexDirection:'row', justifyContent:'space-between', paddingTop:10}}>
                  <TouchableHighlight onPress={() => {
                    console.log("Forgot password")
                    this.props.navigation.navigate("ForgotPassword", null)
                  }}>
                    <Text style={{color:'red'}}>Forgot password</Text>
                  </TouchableHighlight>

                  <TouchableHighlight onPress={() => {
                    console.log("New user")
                    this.props.navigation.navigate("NewUser", null)
                  }}>
                    <Text style={{color:'red'}}>New user? SIGN UP</Text>
                  </TouchableHighlight>
                </View>
            </Card>
        </Content>
      </Container>            
    )
  }
}

const styles = StyleSheet.create({
  logo: {
    width: screenWidth/2,
    height: screenWidth/2,  //Since it's s square image
    
  },

  imageView: {
    alignItems:'center',
    justifyContent:'center'
  }
})

function filler(user, pass) { //This is a stub function for a backend call
  let num = (Math.random() * 10) % 2;

  if (num < 1) {
    return false;

  } else {
    return elem = {
      userName: 'ianMckechnie@email.ca',
      userFirstName:'Ian',
      userLastName:'Mckechnie',
      userId:'1234566789',
      userAge:20,
    }
  }
}
 
const mapStateToProps = state => ({
  loggedIn: state || [],
})

const mapDispatchToProps = {
  setLoggedIn: setLoggedIn,
  getLoginInfo: getLoginInfo,
  logOut: logOut
}

const connectToStore = connect(
  mapStateToProps
)
// and that function returns the connected, wrapper component:
const ConnectedComponent = connectToStore(Login)

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Login)