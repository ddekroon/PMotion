import React from 'react'
import {Text, View, StyleSheet, TouchableOpacity, TouchableHighlight, Image } from 'react-native'
import Header from '../components/common/Header'
import Previousleagues from './PreviousLeagues'
import { Container, Content, Card, Button } from 'native-base'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import ToastHelpers from '../../utils/toasthelpers';
import Colors from '../../../native-base-theme/variables/commonColor';
import DueDates from '../../utils/registrationDueDates'

//Temp
import { logOut } from '../../actions/login';


let tempName = 'userFN'   //We will get the users first name from redux once they log in

class Register extends React.Component {

  static propTypes = {
    seasons: PropTypes.object.isRequired,
    isLoading: PropTypes.bool.isRequired,
    leagues: PropTypes.object.isRequired,
    logOut: PropTypes.func.isRequired,
    sports: PropTypes.array.isRequired,
    team: PropTypes.object.isRequired,
  }
  
  render() {
    const { navigation } = this.props;

    //Same error as the login
    /*if (this.props.route?.params?.toastMessage && !starterShown) {
      ToastHelpers.showToast(null, this.props.route?.param.toastString);
    }*/

    return (
      <Container>
        <Content>
          <Card style={{paddingLeft:10}}>
            <View style={{flexDirection:'row', justifyContent:'space-around', paddingBottom:10, paddingTop:10}}>
              
              <Button rounded light onPress={() => {
                navigation.navigate('profile')
              }}>
                <Text>  My Profile  </Text>
              </Button>

              <Button rounded light onPress={() => {  //Probably gonna have problems here, needs to reset the redux store when you log out incase they log back in as another user
                this.props.logOut();
              }}>
                <Text>  Log Out   </Text>
              </Button>

              <Button rounded light onPress={() => {
                navigation.navigate('waivers');
              }}>
                <Text>  Sign Waivers  </Text>
              </Button>

            </View>
          </Card>

          <Card style={{paddingLeft:10}}>
            <View>
              <View>
                <Header
                  title={"Hello " + tempName}
                  content="Pick what you would like to register in."
                />
              </View>
              <View style={{paddingBottom:15, paddingTop:10, alignItems:'center'}}>
              
                <TouchableOpacity 
                  onPress={ () => {
                    navigation.navigate('PickSport', {registerType:'IndividualRegister'})
                  }}
                  style={styles.button}
                >
                  <Text style={styles.text}>Register as an Indivdual or Small Group </Text>
                </TouchableOpacity>
                <View style={{paddingBottom:20}}/>

                <TouchableOpacity 
                  onPress={ () => {
                    navigation.navigate('Previousleagues', {use: 'reregister'})
                  }}
                  style={styles.button}
                >
                  <Text style={styles.text}>Re-register Previous Team</Text>
                </TouchableOpacity>
                <View style={{paddingBottom:20}}/>

                <TouchableOpacity 
                  onPress={ () => {
                    navigation.navigate('PickSport', {registerType:'RegisterNewTeam'})
                  }}
                  style={styles.button}
                >
                  <Text style={styles.text}>Register New Team</Text>
                </TouchableOpacity>
              </View>
            </View>
          </Card>

          <Card style={{paddingLeft:10}}>

            <Text style={styles.header}>Registration Due By</Text>
            <View style={styles.line}/>

            <View style={{flexDirection:'row', }}>
              
              <View style={{paddingRight:5}}>
                <Text>Ultimate Frisbee</Text>
                <View style={{paddingBottom:10}}/>
          
                <Text>Beach Volleyball</Text>
                <View style={{paddingBottom:10}}/>

                <Text>Flag Football</Text>
                <View style={{paddingBottom:10}}/>

                <Text>Soccer</Text>  
                <View style={{paddingBottom:10}}/>       
              </View>

              <View>
                <Text style={{color:Colors.brandSecondary}}>{DueDates.ultimate}</Text>
                <View style={{paddingBottom:10}}/>
          
                <Text style={{color:Colors.brandSecondary}}>{DueDates.volleyball}</Text>
                <View style={{paddingBottom:10}}/>

                <Text style={{color:Colors.brandSecondary}}>{DueDates.football}</Text>
                <View style={{paddingBottom:10}}/>

                <Text style={{color:Colors.brandSecondary}}>{DueDates.soccer}</Text>         
                <View style={{paddingBottom:10}}/>
              </View>
            </View>
          </Card>
          
          <Previousleagues/>
          
          <Card style={{paddingLeft:10}}>
            <Text style={styles.header}>Register a New Team</Text>
            <Text style={styles.subHeading}>Select a league logo below to start a new registration</Text>
            <View style={[styles.line, {paddingBottom:40}]}/>
            <View style={{
                flex: 1,
                flexDirection: 'column',
                justifyContent: 'space-between',
                alignItems:'center'
            }}>
              
              <View style={{paddingTop:50}}/>
              <TouchableHighlight
                onPress={() => {
                  navigation.navigate('RegisterNewTeam', {sport:1})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                      source ={ require('../../images/ultimate-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:50}}/>
              <TouchableHighlight  onPress={() => {
                navigation.navigate('RegisterNewTeam',{sport:2})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                    source ={ require('../../images/volleyball-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:50}}/>
              <TouchableHighlight  onPress={() => {
                navigation.navigate('RegisterNewTeam',{sport:3})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                    source ={ require('../../images/football-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:50}}/>
              <TouchableHighlight  onPress={() => {
                  navigation.navigate('RegisterNewTeam',{sport:4})  //Runs when the user selects it from the registration page
                  
              }}>
                  <Image
                      source ={ require('../../images/soccer-small.png')}
                  />
              </TouchableHighlight >
            </View>
          </Card>
        </Content>
      </Container>
    )
  }
}

const styles = StyleSheet.create({ 
  button: {
    width:'80%',
    backgroundColor:Colors.brandSecondary,
    borderRadius: 10,
    flexDirection:'column',
    justifyContent: 'center',
    alignItems:'center',
    height:100
  },

  text: {
    color:'white',
  },

  header: {
    fontWeight:'bold',
  },

  subHeading: {
      color: '#474747',
  },

  line: {
      borderBottomColor:'black',
      borderBottomWidth:1,
      paddingBottom:10
  },
})

const mapStateToProps = state => ({
  seasons: state.lookups.scoreReporterSeasons || [],
  leagues: state.leagues || {},
  sports: state.lookups.sports || [],
  isLoading: state.status.loading || false,
  TeamSubmisson: state.TeamSubmisson,
  team: state.teams,
  user: state.currentUser || {}
})

const mapDispatchToProps = { 
  logOut: logOut
}

const connectToStore = connect(
  mapStateToProps
)
  // and that function returns the connected, wrapper component:
const ConnectedComponent = connectToStore(Register)

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Register)