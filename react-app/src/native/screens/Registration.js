import React from 'react'
import {Text, View, StyleSheet, TouchableOpacity, TouchableHighlight, Image } from 'react-native'
import Header from '../components/common/Header'
import Previousleagues from './PreviousLeagues'
import { Container, Content, Card, Button } from 'native-base'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import { saveWaiverToState } from '../../actions/waiver'
import ToastHelpers from '../../utils/toasthelpers';
import Colors from '../../../native-base-theme/variables/commonColor';

  class Register extends React.Component {

  static propTypes = {
    seasons: PropTypes.object.isRequired,
    isLoading: PropTypes.bool.isRequired,
    leagues: PropTypes.object.isRequired,
    addWaiver: PropTypes.func.isRequired,
    sports: PropTypes.array.isRequired,
    team: PropTypes.object.isRequired,
  }
  
  render() {
    const { navigation } = this.props;
    let starterShown = false

    if (this.props.route?.params?.showToast && !starterShown) {
      ToastHelpers.showToast(null, this.props.route.params.toastString);
      //starterShown = !starterShown
    }

    return (
      <Container>
        <Content>
          <Card>
            <View style={{flexDirection:'row', justifyContent:'space-around', paddingBottom:10, paddingTop:10}}>
              
              <Button rounded light onPress={() => {
                navigation.navigate('Login', {registerType:'Profile'})
              }}>
                <Text>  My Profile  </Text>
              </Button>

              <Button rounded light onPress={() => {
                navigation.navigate('waivers');
              }}>
                <Text>  Sign Waivers  </Text>
              </Button>

            </View>
          </Card>

          <Card>

            <View>
              <View>
                <Header
                  title="Hello"
                  content="Pick what you would like to register in."
                />
              </View>
              <View style={{paddingBottom:10}}/>
              
              <TouchableOpacity 
                onPress={ () => {
                  navigation.navigate('PickSport', {registerType:'individualRegister'})
                }}
                style={styles.button}
              >
                <Text style={styles.text}>Register as an Indivdual or Small Group </Text>
              </TouchableOpacity>
              <View style={{paddingBottom:20}}/>

              <TouchableOpacity 
                onPress={ () => {
                  navigation.navigate('Login', {registerType:'reregister'})
                }}
                style={styles.button}
              >
                <Text style={styles.text}>Re-register Previous Team</Text>
              </TouchableOpacity>
              <View style={{paddingBottom:20}}/>

              <TouchableOpacity 
                onPress={ () => {
                  navigation.navigate('Login', {registerType:'newTeam'})
                }}
                style={styles.button}
              >
                <Text style={styles.text}>Register New Team</Text>
              </TouchableOpacity>
              <View style={{paddingBottom:15}}/>
            </View>
          </Card>

          <Card>
            <View>
              <Text style={styles.header}>Registration Due By</Text>
              <View style={styles.line}/>
              <View style={{paddingBottom:20}}/>

              <Text>Ultimate Frisbee
                <Text style={{color:'red'}}>      INSERT DATA</Text>
              </Text>
              <View style={{paddingBottom:10}}/>
        
              <Text>Beach Volleyball
                <Text style={{color:'red'}}>      INSERT DATA</Text>
              </Text><View style={{paddingBottom:10}}/>

              <Text>Flag Football
                <Text style={{color:'red'}}>      INSERT DATA</Text>
              </Text>
              <View style={{paddingBottom:10}}/>

              <Text>Soccer
                <Text style={{color:'red'}}>      INSERT DATA</Text>
              </Text>         
              <View style={{paddingBottom:10}}/>   
            </View>
          </Card>
          
          <Card>
            <Previousleagues/>
          </Card>
          
          <Card>
            <Text style={styles.header}>Register a New Team</Text>
            <Text style={styles.subHeading}>Select a league logo below to start a new registration</Text>
            <View style={[styles.line, {paddingBottom:40}]}/>
            <View style={{
                flex: 1,
                flexDirection: 'column',
                justifyContent: 'space-between',
                alignItems:'center'
            }}>
              
              <TouchableHighlight  onPress={() => {
                navigation.navigate('RegisterNewTeam', {sport:1})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                      source ={ require('../../images/ultimate-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:10}}/>
              <TouchableHighlight  onPress={() => {
                navigation.navigate('RegisterNewTeam',{sport:2})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                    source ={ require('../../images/volleyball-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:10}}/>
              <TouchableHighlight  onPress={() => {
                navigation.navigate('RegisterNewTeam',{sport:3})  //Runs when the user selects it from the registration page
              }}>
                  <Image
                    source ={ require('../../images/football-small.png')}
                  />
              </TouchableHighlight >

              <View style={{paddingTop:10}}/>
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
    width:'100%',
    backgroundColor:Colors.brandSecondary,
    borderRadius: 5,
    textAlign:'center',
    fontWeight: 'bold',
    //height: 40,
    flexDirection:'column',
    justifyContent: 'space-between',
    alignItems:'center',
  },

  text: {
    color:'white',
    fontSize:Colors.fontSizeH2,
    textAlign:'center',
  },

  header: {
    fontWeight:'bold',
    fontSize:Colors.fontSizeH1
  },

  subHeading: {
      color: '#474747',
      fontSize:Colors.fontSizeH3,
  },

  line: {
      borderBottomColor:'black',
      borderBottomWidth:1,
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
  addWaiver: saveWaiverToState,
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