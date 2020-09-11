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
import { setMessageToRead, addMessage } from '../../actions/toastMessage';
import ToastHelpers from '../../utils/toasthelpers'
import { Image, StyleSheet, TouchableHighlight } from 'react-native';
import Colors from '../../../native-base-theme/variables/commonColor';

const screenWidth = Colors.deviceWidth; //needs to reference commonColours, Not remake this var

class Login extends React.Component{

  static propTypes = {
    setLoggedIn: PropTypes.func.isRequired,
    getLoginInfo: PropTypes.func.isRequired,
    logOut: PropTypes.func.isRequired,
    addMessage: PropTypes.func.isRequired,
    setMessageToRead: PropTypes.func.isRequired,
  }
    
  constructor(props) {
    super(props)
    this.state = {
      username:'',
      password:''
    }
  }
    
  render() {
  
    //Throws errors here
    /*console.log("Props = " + JSON.stringify(this.props.loggedIn.toastMessage.toBePrinted))
    if (this.props.loggedIn.toastMessage.toBePrinted) {
      ToastHelpers.showToast(this.props.loggedIn.toastMessage.message, null)
      this.props.setMessageToRead();
    }*/

    return (
      <Container>
        <Content>
          
            <Card style={{paddingLeft:10}}>
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
                    let obj = new Object;
                    obj.userName = this.state.username
                    obj.password = this.state.password
                    //Log in with object here

                    this.props.setLoggedIn(true)  //Put in the users information returned from the server in here to save it to the redux state
                }}>
                  <Text>Login</Text>
                </Button>
              </View>

              <View style={{flexDirection:'row', justifyContent:'space-between', paddingTop:10}}>
                  <TouchableHighlight onPress={() => {
                    this.props.navigation.navigate("ForgotPassword", null)
                  }}>
                    <Text style={{color:'red'}}>Forgot password</Text>
                  </TouchableHighlight>

                  <TouchableHighlight onPress={() => {
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
 
const mapStateToProps = state => ({
  loggedIn: state || [],
  toBePrinted: state || '',
  message: state || false,
})

const mapDispatchToProps = {
  setLoggedIn: setLoggedIn,
  getLoginInfo: getLoginInfo,
  logOut: logOut,
  setMessageToRead: setMessageToRead,
  addMessage: addMessage,
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