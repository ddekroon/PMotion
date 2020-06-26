import React, {useState} from 'react'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import {Icon, Picker } from 'native-base'
import { fetchLeague } from '../../actions/leagues' //Gets the leagues from the web.
import DateTimeHelpers from '../../utils/datetimehelpers'
import {TextInput, Text, View, Button, Alert} from 'react-native'
import {StyleSheet} from 'react-native'
import { ScrollView } from 'react-native-gesture-handler';
import AddingTeamMembers from '../components/register/TeamRegisterNewTeammate'
import { submitTeam } from '../../actions/teams'

import {
    saveTeamToState,
    reset
} from '../../actions/teams'

//Expects a sport id given as 1-4 under the 'sport' props tag

/** also expects this to be a prop given named as user
     * 
     * const user = {
        user:'imckechn',
        FN:'Ian',
        LN:'McKechnie',
        email:'imckechn@uoguelph.ca',
        phone:'9056915041',
        sex:'Male',
    }
     */

class RegisterTeam extends React.Component { 
  
    //I make a server call to get all the leagues. In the props (sport=(1-4)) the correct leagues for that sport appear
    static propTypes = {
        seasons: PropTypes.object.isRequired,
        isLoading: PropTypes.bool.isRequired,
        leagues: PropTypes.object.isRequired,
        getLeague: PropTypes.func.isRequired,
        onSubmit: PropTypes.func.isRequired,
        team: PropTypes.object.isRequired,
        saveTeamToState: PropTypes.func.isRequired,
        reset: PropTypes.func.isRequired
    }
    
    constructor(props) {
        super(props)
        this.state = {
            comment:'',
            hearMethod:'',
            paymentMethod:'',
            league:'',
            teamName:'',
            count:0,
            jsonPlayers:[]
        }

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

    update = (newJson) => {
        let arr = this.state.jsonPlayers
        arr[newJson.index] = newJson

        this.setState({jsonPlayers:arr})
    }

    updateLeague = (league) => {
         this.setState({ league: league })
    }

    checkAnswers() {
        let str = "You're missing information!";
        let noError = true

        if (this.state.league == undefined) {
            noError = false
            str += ('\n-No league selected')
        }
        if (this.state.teamName == undefined) {
            noError = false
            str += ('\n- No team name chosen')
        }
        if (this.state.paymentMethod == undefined) {
            noError = false
            str += ('\n- No payment method selected')
        }

        if (noError) {
            return false

        } else {
            alert(str)
            return true
        }
    }

    handleSubmit = (seasons) => {

        if (this.checkAnswers()) return;
        console.log('sucess')
        
        //Creating the object that will be uploaded to redux/the server
        let obj = new Object

        //Get the league ID
        let league;

        seasons[this.props.sport][0].leagues.map( (curLeague) => {
            if (this.state.league == curLeague.name) {
                obj.leagueId = curLeague.id;
                league  = curLeague //Need this later 
                return;
            }
        })

        obj.name = this.state.teamName
        obj.wins = 0
        obj.loses = 0
        obj.ties= 0

        let t = new Date()
        obj.dateCreate = {
            date : t.getFullYear() + "-"
             + t.getMonth() + "-"
              + t.getDay() + " "
               + t.getHours() + ":"
                + t.getMinutes() + ":"
                 + t.getMilliseconds(),
            timezone_type : 3,  //Hardcode as I don't know where this comes from and all the examples its 3, same with the line bellow
            timezone : 'America/Toronto'
        }
        obj.isFinalized = false
        obj.isPaid = 0
        obj.isDeleted = 0
        obj.isDroppedOut = false
        obj.submittedWins = 0
        obj.submittedLoses = 0
        obj.submittedTies = 0
        obj.oppSubmittedWins = 0
        obj.oppSubmittedLosses = 0
        obj.oppSubmittedTies = 0
        obj.league = league
        
        let captain = new Object
        captain.fn = this.state.FN
        captain.ln = this.state.LN
        captain.email = this.state.email
        captain.sex = this.state.sex

        obj.captain = captain

        obj.players = this.state.jsonPlayers
        obj.registrationComment = this.state.comment

        //Get a random ID for the team
        let unique = false;
        let id = Math.round(Math.random() * 10000) 

        while (!unique) {
            for (team in this.props.team) {
                if (team == id) {
                    unique = false
                    id = Math.round(Math.random() * 10000) 
                    return
                } else {
                    unique = true
                }
            }
        }
        
        obj.id = id
        
        //console.log("What will be saved: " + JSON.stringify(obj))
        this.props.saveTeamToState(obj)
    }

    render() {
        let counter = 0;

        if (!seasons) console.log("Error loading seasons")

        const {
        loading,
        seasons
        } = this.props

        return (
            <ScrollView>
                <Text style={styles.header, styles.addPadding}>Team Register</Text>
                <View style={styles.addPadding}>
                <View style={styles.setHorizontal}>
                    <Text style={styles.normalText}>League
                        <Text style={styles.normalText, {color:'red'} }>*</Text>
                    </Text>
                    <Picker
                    placeholder="League"
                    note={false}
                    mode="dropdown"
                    iosIcon={<Icon name="arrow-down" />}
                    selectedValue = {this.state.league}
                    onValueChange={this.updateLeague}
                    style = {{
                        borderWidth: 1,
                        alignItems: 'center',
                        flexDirection:'row',
                        justifyContent: 'center',
                        //Should be centered :/
                    }}
                    >

                    <Picker.Item key={0} label={'League'} value={''} />
                        {seasons[this.props.sport][0].leagues.map(curLeague => {
                            //console.log("Seasons  = " + JSON.stringify(seasons))
                            var leagueName =
                                curLeague.name +
                                ' - ' +
                                DateTimeHelpers.getDayString(curLeague.dayNumber)

                            return (
                                <Picker.Item
                                key={curLeague.id}
                                label={leagueName}
                                value={curLeague.id}
                                />
                            )
                        })}
                    </Picker>
                </View>

                <View style={styles.setHorizontal}>
                    <Text style={styles.normalText}>Team Name
                        <Text style={styles.normalText, {color:'red'} }>*</Text>
                    </Text>
                    <TextInput style={styles.textInput} onChangeText={(val) => this.setState({teamName:val})} value={this.state.teamName}/>
                    </View>
                </View>

                <View style={styles.main}>
                    <View style={styles.padding}>
                    <Text style={{fontSize:20, fontWeight:'bold'}}>Team Captain</Text>
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

                <View>
                    <Text style={styles.header}>Player Information</Text>
                    <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                    <View style= {styles.line}/>

                    <View style={styles.stack}>
                        <View style={styles.stack}>
                            {counter = -1, this.state.jsonPlayers?this.state.jsonPlayers.map( (elem) => {
                                counter++
                                return (<View style={styles.padding} key={counter}>
                                        <AddingTeamMembers json={elem} func={this.update}/>
                                    </View>)
                            }):null}
                        </View>

                        <View style={styles.setHorizontal}>
                            <Button style={styles.botButton} title={'Add Player'} onPress={() => {
                                //Check for max list lenght

                                if (this.state.jsonPlayers && this.state.jsonPlayers.length == 14) {
                                    alert("Cannot have a team size greater than 15")
                                } else {
                                    
                                    //create the object and set its index to the current count (then update the count)
                                    let obj = new Object
                                    obj.index = this.state.count?this.state.count:0
                                    this.setState({count: obj.index + 1})

                                    obj.firstName = ''
                                    obj.lastName = ''
                                    obj.email = ''
                                    obj.sex = ''
                                    obj.key = obj.index

                                    let arr = this.state.jsonPlayers?this.state.jsonPlayers:[]
                                    arr.push(obj)
                                    this.setState({jsonPlayers:arr})
                                }
                            }}/>

                            <Button title={'Remove Player'} style={styles.botButton} onPress={() => {
                                let arr = this.state.jsonPlayers
                                arr?arr.pop():alert("No teammates created yet.")

                                this.setState({jsonPlayers:arr})
                            }}/>
                        </View>
                    </View>
                </View>

                <View>
                    <Text style={styles.header}>Comments</Text>
                    <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                    <View style= {styles.line}/>
                    
                    <View style={styles.addPadding}>
                        <View style={ [styles.setHorizontal, styles.addPadding, styles.commentView]}>
                            <Text style={styles.normalText}>Comments </Text>
                            <TextInput
                                style={styles.textInput}
                                placeHolder={'Comment here...'}
                                multiLine={true}
                                onChangeText= { (comment) => this.setState( {comment})}
                                value={this.state.comment}                    
                            />
                        </View>
                        
                        <View style={styles.addPadding, {justifyContent:'center', alignItems:'center' }}>
                            <Text style={styles.normalText}>How did you hear about us?</Text>
                            <View>
                                <Picker
                                    placeholder='Choose Method'
                                    mode="dropdown"
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.commentsPicker}
                                    selectedValue = {this.state.hearMethod}
                                    onValueChange={ (method) => { this.setState({hearMethod:method})} }
                                >
                                    <Picker.Item label="Choose Method" value='' key={0} />
                                    <Picker.Item label="Google/Internet Search" value='Google/Internet Search' key={1} />
                                    <Picker.Item label="Facebook Page" value='Facebook Page' key={2} />
                                    <Picker.Item label="Kijiji Ad" value='Kijiji Ad' key={3} />
                                    <Picker.Item label="Returning Player" value='Returning Player' key={4} />
                                    <Picker.Item label="From a Friend" value='From a Friend' key={5} />
                                    <Picker.Item label="Restaurant Ad" value='Restaurant Ad' key={6} />
                                    <Picker.Item label="The Guelph Community Guide" value='The Guelph Community Guide' key={7} />
                                    <Picker.Item label="Other" value='Other' key={8} />
                                    
                                </Picker>
                            </View>
                        </View>
                        
                    </View>

                    <Text style={styles.header}>Confirm Fees</Text>
                    <Text style = {styles.subHeading}>The registration process is not finalized until fees have been paid.</Text>
                    <View style= {styles.line}/>
                    <View style={styles.addPadding}>
                        <View style={ [styles.setHorizontal, styles.addPadding], {justifyContent:'center', alignItems:'center', paddingVertical:20 }}>
                            <Text style={styles.normalText}>Method
                                <Text style={styles.normalText, {color:'red'} }>*</Text>
                            </Text>

                            <View>
                                <Picker
                                    placeholder='Choose Method'
                                    mode={'dropdown'}
                                    note={false}
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.commentsPicker}
                                    selectedValue = {this.state.paymentMethod}
                                    onValueChange={ (itemValue, itemIndex) => this.setState({paymentMethod:itemValue}) }
                                >
                                    <Picker.Item label={"Choose Method"} value={''} key={0} />
                                    <Picker.Item label={"I will send an money email transfer to dave@perpetualmotion.org"} value={'I will send an money email transfer to dave@perpetualmotion.org'} key={1} />
                                    <Picker.Item label={"I will mail a cheque to the Perpetual Motion head office"} value={'I will mail a cheque to the Perpetual Motion head office'} key={2} />
                                    <Picker.Item label={"I will bring a cash/cheque to the Perpetual Motion head office"} value={'I will bring a cash/cheque to the Perpetual Motion head office'} key={3} />
                                    <Picker.Item label={"I will bring cash/cheque to registration night"} value={'I will bring cash/cheque to registration night'} key={4} />
                                    
                                </Picker>
                            </View>
                        </View>
                        
                        <Text style={ [styles.normalText, {fontWeight:'bold'}]}>Make Checks Payable to Perpetual Motion</Text>
                        <Text style={ [styles.normalText, {fontWeight:'bold'}]}>Send This Confirmation Form & Fees to:</Text>
                        <Text style={styles.normalText}>78 Kathleen St. Guelph, Ontario; H1H 4Y3</Text>
                    </View>

                    <Text style={styles.header}>Registration Due By</Text>
                    <View style= {styles.line}/>
                    <View style={styles.addPadding}>
                        <Text style={ [styles.normalText, {fontWeight:'bold'}]}>Spring League</Text>

                        <Text style={styles.normalText}>Ultimate Frisbee
                            <Text style={{color:'#FF0000'}}>    *Insert date Here*</Text>
                        </Text>

                        <Text style={styles.normalText}>Beach Volleyball
                            <Text style={{color:'#FF0000'}}>    *Insert date Here*</Text>
                        </Text>

                        <Text style={styles.normalText}>Flag Football
                            <Text style={{color:'#FF0000'}}>    *Insert date Here*</Text>
                        </Text>

                        <Text style={styles.normalText}>Soccer
                            <Text style={{color:'#FF0000'}}>    *Insert date Here*</Text>
                        </Text>

                    </View>

                    <Text style={styles.header}>Register</Text>
                    <Text style = {styles.subHeading}>Submit your group registration to the convenor.</Text>
                    <View style= {styles.line}/>
                    <View style={styles.addPadding, {justifyContent:'space-between', flexDirection:'row'}}>
                        <Button title={'register (Submit)'} color='red' onPress={() => {
                            this.handleSubmit(seasons)
                        }}/>
                        <Button /*title={'Print Forum'}*/ title={'practice clicking+'} color='red' onPress={() => {
                            this.props.reset()   
                        }}/>
                        <Button title={'Delete old tags'} color='red' onPress={() => {
                            this.props.reset()
                        }}/>
                    </View>
                </View>
                <Button title={"Get team props "} onPress={() => {
                    //console.log("Team Obj = " + JSON.stringify(this.props.team))
                }}/>
            </ScrollView>
        )
    }
}

//The picker function for chosing the sex of the player
export const dropDownSex = (sex) => {
    let chosen = ''
    if (sex) chosen = (sex);
    
    return (
        <View>
            <Picker
                placeholder='Sex'
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={ styles.picker}
                selectedValue = {chosen}
                onValueChange={ (sex) => chosen = (sex) }
            >
                <Picker.Item label='Sex' value='Sex' key={0} />
                <Picker.Item label="Male" value="Male" key={1} />
                <Picker.Item label="Female" value="Female" key={2} />
            </Picker>
        </View>
    );
}

const styles = StyleSheet.create({
  header: {
      fontWeight:'bold',
      fontSize:35
  },

  subHeading: {
      color: '#474747',
      fontSize:12,
  },

  line: {
      borderBottomColor:'black',
      borderBottomWidth:1,
  },  

  picker: {
      borderWidth:1,
      justifyContent: 'flex-start',
      width:200
  },

  setHorizontal: {
      flexDirection:'row',
      
  },  

  textInput: {
      borderBottomWidth:1,
      borderBottomColor:'red',
      width:'75%'
  
  },

  normalText: {
      fontSize:20,
  },

  addPadding: {
      paddingBottom:20,
  },

  commentView: {
      height:40,
      justifyContent: 'center',
      alignItems: 'center'
  },

  botButton: {
      backgroundColor:'red',
      borderRadius:10,
  },

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
  
  commentsPicker: {
      borderWidth:1,
      justifyContent: 'flex-start',
      width:300
  },

  main: {
      backgroundColor:'white',
      borderRadius:15,
  },

  padding: {
      padding:10
  },

  stack: {
      flexDirection:'column'
  }
})

const mapStateToProps = state => ({
  seasons: state.lookups.scoreReporterSeasons || [],
  leagues: state.leagues || {},
  isLoading: state.status.loading || false,
  TeamSubmisson: state.TeamSubmisson,
  team: state.teams,
  user: state.currentUser || {}
})

const mapDispatchToProps = { 
  getLeague: fetchLeague,
  onSubmit: submitTeam,
  saveTeamToState: saveTeamToState,
  reset: reset
}

const connectToStore = connect(
    mapStateToProps
)
  // and that function returns the connected, wrapper component:
//const ConnectedComponent = connectToStore(RegisterTeam) //dont think i need this.

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RegisterTeam)