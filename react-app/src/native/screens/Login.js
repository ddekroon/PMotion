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
      <Container style={styles.container}>
        <View style={styles.content}>
          <Form>
            <Item style={{...styles.item, ...styles.formGroup }}>
              <Input
                placeholder='Username'
                autoCapitalize="none"
                value={this.state.username}
                onChangeText={username => this.setState({username:username})}
              />

            </Item>

            <Item style={{...styles.item, ...styles.formGroup }}>
              <Input
                placeholder='Password'
                secureTextEntry
                value={this.state.password}
                onChangeText={pass => this.setState({password:pass})}
              />

              <Spacer size={20} />
            </Item>

            <View style={{flexDirection:'row', justifyContent:'space-between', ...styles.formGroup}}>
              <TouchableHighlight onPress={() => {
                this.props.navigation.navigate("ForgotPassword", null)
              }}>
                <Text style={{color:'red'}}>Forgot password</Text>
              </TouchableHighlight>

              <TouchableHighlight onPress={() => {
                this.props.navigation.navigate("NewUser", null)
              }}>
                <Text style={{color:'red'}}>New user</Text>
              </TouchableHighlight>
            </View>
        
            <Button block title={'Login'} onPress={() => {
                let obj = new Object;
                obj.userName = this.state.username
                obj.password = this.state.password
                //Log in with object here

                this.props.setLoggedIn(true)  //Put in the users information returned from the server in here to save it to the redux state
            }}>
              <Text>Login</Text>
            </Button>
          </Form>
        </View>
      </Container>
    )
  }
}

const styles = StyleSheet.create({
  container: {
    flex:1, 
    justifyContent:'center'
  },
  content: {
    padding:10
  },
  item: { marginLeft: 0 },
  formGroup: { marginBottom: 10 }
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