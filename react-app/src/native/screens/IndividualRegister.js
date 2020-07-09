//This does not need any props handed to it when creating the tag for it 

import React from 'react'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import {Icon, Picker } from 'native-base'
import { fetchLeague } from '../../actions/leagues' //Gets the leagues from the web.
import {TextInput, Text, View, Button, Modal, TouchableHighlight} from 'react-native'
import {StyleSheet} from 'react-native'
import { ScrollView } from 'react-native-gesture-handler';
import { submitTeam } from '../../actions/teams'
import ToastHelpers from '../../utils/toasthelpers'
import ValidationHelpers from '../../utils/validationhelpers'
import TeamPicker from '../components/common/TeamPicker'
import AddingTeamMembersIndividual from '../components/register/IndividualTeamMemberPlayerAdder'
import {
    saveTeamToState,
    reset
} from '../../actions/teams'

class IndividualRegister extends React.Component {
    static propTypes = {
        seasons: PropTypes.object.isRequired,
        isLoading: PropTypes.bool.isRequired,
        leagues: PropTypes.object.isRequired,
        getLeague: PropTypes.func.isRequired,
        onSubmit: PropTypes.func.isRequired,
        sports: PropTypes.array.isRequired,
        team: PropTypes.object.isRequired,
        saveTeamToState: PropTypes.func.isRequired,
        reset: PropTypes.func.isRequired
    }

    constructor(props) {
        super(props)
        this.state = {
            chsnSport:'',
            modalVisible: false,
            comment:'',
            source:'',
            paymentMethod:'',
            teamName:'',
            count:0,
            players:[],
            league:['', '', '']
        }
    }

    //When the user changes the sport, update the state and reset the league choices
    sportChange = (sport) => {
        this.setState({chsnSport:sport})
        this.setState({league:['', '', '']})    //I.e reset it")
    }

    //The info button to display the league difficulties
    setModalVisible = (visible) => {
        this.setState({ modalVisible: visible });
    }

    //When a new players info get changed, the update gets put into the state here
    update = (newJson) => {
        let obj = newJson

        let arr = this.state.players
        arr[obj.key] = newJson

        this.setState({players:arr})
    }

    //When submitting the forum, this checks that all the required fields are filled out
    checkAnswers() {
        let str = "You're missing information!";
        let noError = true

        if (this.state.league[0] == '') {
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

        for (player in this.state.jsonPlayers) {
            if (this.state.jsonPlayers[player].email) {
                if ( !ValidationHelpers.isValidEmail(this.state.jsonPlayers[player].email) ) {
                    noError = false
                    str += ('\n- A players email is invalid')
                    break
                }
            }

            if (this.state.jsonPlayers[player].phone) {
                if (this.state.jsonPlayers[player].phone.length != 0 && this.state.jsonPlayers[player].phone.lenght != 10) {
                    noError = false
                    str += ('\n- A players phone number has an invalid length')
                    break
                }
            }
        }

        if (this.state.players.length == 0) {
            noError = false;
            str += ('\n- You need at least one player')

        } else {

            if (this.state.players[0].fn == undefined || this.state.players[0].fn == '' ) {
                noError = false;
                str += ('\n- Your first players first name needs to be filled in')
                    
            } 

            if (this.state.players[0].ln == undefined || this.state.players[0].ln == '' ) {
                noError = false;
                str += ('\n- Your first players last name needs to be filled in')

            } 

            if (this.state.players[0].email == undefined || this.state.players[0].email == '' ) {
                noError = false;
                str += ('\n- Your first players email needs to be filled in')

            } 

            if (this.state.players[0].phone == undefined || this.state.players[0].phone == '' ) {
                noError = false;
                str += ('\n- Your first players phone number needs to be filled in')

            } 

            if (this.state.players[0].sex == undefined || this.state.players[0].sex == '' ) {
                noError = false;
                str += ('\n- Your first players sex needs to be filled in')
            
            } 

            if (this.state.players[0].skill == undefined || this.state.players[0].skill == '' ) {
                noError = false;
                str += ('\n- Your first players skill needs to be filled in')            
            }
        }

        if (noError) {
            return false

        } else {
            ToastHelpers.showToast(null, str)
            return true
        }
    }

    //This submits the forum to the server (Takes everything in the state and save it)
    handleSubmit = () => {
        if (this.checkAnswers()) return;
        
        let obj = new Object
        let league;

        this.props.seasons[this.state.chsnSport][0].leagues.map( (curLeague) => {
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
        obj.captain = ''

        obj.preferedLeague = this.state.league[0]
        obj.secondaryLeague = this.state.league[1]?this.state.league[1]:''
        obj.tertiaryLeague = this.state.league[2]?this.state.league[2]:''

        obj.players = this.state.players
        obj.registrationComment = this.state.comment

        //Get a random ID for the team
        let unique = false;
        let id; 

        while (!unique) {
            id = Math.round(Math.random() * 10000);
            
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
        
        //console.log("THE FINAL OBJ = " + JSON.stringify(obj))
        this.props.saveTeamToState(obj)
        console.log("props = " + JSON.stringify(this.props))

        //  To submit 
        /*const {onSubmit} = this.props
        onSubmit().catch(e => {
            console.log("Encountered an error submitting the data")
        })*/
    }

    //Returns the array of all the leagues for the chosen sport for the league pickers to use
    getLeaguesArr = (sportId) => {
        let counter = 1;

        for (seasonId in this.props.seasons) {
            if (counter == sportId) {
                return this.props.seasons[seasonId][0].leagues
            }
            counter++
        }
        
        return []
    }

    //When the user changes their league choice, it gets updated in the state here
    changedLeague = (index, value) => {
        let arr = this.state.league
        arr[index] = value
        this.setState({league:arr})
    }

    render() {
        console.log("The route = " + JSON.stringify(this.props.route))

        const {
            loading,
            seasons
        } = this.props
        const { modalVisible } = this.state

        if (!seasons) console.log("Error loading seasons")
        if (this.state.modalVisible == undefined) this.setState({modalVisible: false})

        return(
            <ScrollView>
                <Text style={styles.header}>Registration</Text>
                <View style={{alignItems:'center', justifyContent:'center'}}>
                    
                    {/*Where the user picks the sport*/}
                    <Picker
                        note={false}
                        mode="dropdown"
                        iosIcon={<Icon name="arrow-down" />}
                        selectedValue={this.state.chsnSport}
                        placeholder="Sport"
                        onValueChange={(val) => {
                            this.sportChange(val)
                        }}
                    >
                        <Picker.Item key={0} label="Sport" value="" />
                        {this.props.sports.map(curSport => {
                            return (
                                <Picker.Item
                                    key={curSport.id}
                                    label={curSport.name}
                                    value={curSport.id}
                                />
                            )
                        })}
                    </Picker>

                    {/**The info part that pops up when pressed */}
                    <Modal
                        animationType="slide"
                        transparent={true}
                        visible={modalVisible}
                    >
                        <View style={styles.centeredView}>
                            <View style={styles.modalView}>
                                
                                <ScrollView>
                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>A:</Text>
                                        <Text>This 7 vs 7 division is recommended for teams and players who would like to play very competitive Ultimate at a high-pace. Players generally have lots of tournament experience and a very strong knowledge of rules and strategies.</Text>
                                    </Text>

                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>B7:</Text>
                                        <Text>This 7 vs 7 division is recommended for teams and players who would like to try playing 7s Ultimate. Players generally have at least a couple years of league experience and are fairly knowledgeable of rules and strategies. </Text>
                                    </Text>

                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>B/B1:</Text>
                                        <Text>This 5 vs 5 division is recommended for teams and players who are of high intermediate skill level. Players generally have a few years of league experience, and a good knowledge of rules and strategies, such as the stack. </Text>
                                    </Text>

                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>B2:</Text>
                                        <Text>This 5 vs 5 division is recommended for teams and players who are of intermediate skill level. Players generally have a couple years of league experience and a decent knowledge of rules and strategies, such as the "stack". </Text>
                                    </Text>

                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>C/C1:</Text>
                                        <Text>This 5 vs 5 division is recommended for teams and players who are of high beginner skill levels. Players generally have at least a year of league experience and a basic knowledge of rules and strategies. </Text>
                                    </Text>

                                    <Text>
                                        <Text style={{fontWeight:'bold'}}>C2:</Text>
                                        <Text>This 5 vs 5 division is recommended for teams and players who are new to the sport of ultimate. Players have less than a year of league experience and have little knowledge of rules and strategies. Players are more focused on learning the game and are less concerned with the skill level.</Text>
                                    </Text>
                                </ScrollView>
                                <TouchableHighlight
                                style={{ ...styles.openButton, backgroundColor: "red", paddingTop:10 }}
                                onPress={() => {
                                    this.setModalVisible(!modalVisible);
                                }}
                                >
                                    <Text style={styles.textStyle}>Close</Text>
                                </TouchableHighlight>
                            </View>
                        </View>
                    </Modal>

                    <TouchableHighlight
                        style={styles.openButton}
                        onPress={() => {
                        this.setModalVisible(true);
                        }}
                    >
                        <Text style={styles.textStyle}>Show League Info</Text>
                    </TouchableHighlight>
                    
                    {/**Where the user picks they're 3 league choices */}
                    <View>
                        <View style={{paddingBottom:5}}>
                            <Text style={styles.normalText}>Prefered League
                                <Text style = {styles.normalText, {color:'red'}}>*</Text>
                            </Text>   
                            <TeamPicker
                                loading={false}
                                teams={this.getLeaguesArr(this.state.chsnSport)}
                                curTeamId={this.state.league[0]}
                                onTeamUpdated={(val) => this.changedLeague(0, val)}
                            />
                        </View>

                        <View style={{paddingBottom:5}}>
                            <Text style={styles.normalText}>Secondary Choice</Text> 
                            <TeamPicker
                                loading={false}
                                teams={this.getLeaguesArr(this.state.chsnSport)}
                                curTeamId={this.state.league[1]}
                                onTeamUpdated={(val) => this.changedLeague(1, val)}
                            />
                        </View>

                        <View style={{paddingBottom:5}}>
                            <Text style={styles.normalText}>Tertiary Choice</Text>  
                            <TeamPicker
                                loading={false}
                                teams={this.getLeaguesArr(this.state.chsnSport)}
                                curTeamId={this.state.league[2]}
                                onTeamUpdated={(val) => this.changedLeague(2, val)}
                            />
                        </View>

                    </View>
                </View>
                <View>
                    <Text style={styles.header}>Player Information</Text>
                    <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                    <View style= {styles.line}/>

                    {/**The loop that shows all the individual user forums */}
                    <View style={styles.stack}>
                        <View style={styles.stack}>
                            {counter = -1, this.state.players?this.state.players.map( (elem) => {
                                counter++
                                return (
                                    <View style={styles.padding} key={counter}>
                                        <AddingTeamMembersIndividual json={elem} func={this.update}/>
                                    </View>)
                            }):null}
                        </View>
                        
                        {/**Add a new player */}
                        <View style={styles.setHorizontal}>
                            <Button style={styles.botButton} title={'Add Player'} onPress={() => {
                                //Check for max list lenght
                                
                                if (this.state.players && this.state.players.length == 15) {
                                    alert("Cannot have a team size greater than 15")
                                } else {
                                    
                                    //create the object and set its key to the current count (then update the count)
                                    let obj = new Object
                                    obj.key = this.state.count?this.state.count:0
                                    this.setState({count: obj.key + 1})

                                    obj.fn = ''
                                    obj.ln = ''
                                    obj.email = ''
                                    obj.phone = ''
                                    obj.sex = ''
                                    obj.skill = ''

                                    let arr = this.state.players?this.state.players:[]
                                    arr.push(obj)
                                    this.setState({players:arr})
                                }
                            }}/>

                            <Button title={'Remove Player'} style={styles.botButton} onPress={() => {
                                let arr = this.state.players
                                arr.pop()

                                this.setState({players:arr})
                            }}/>
                        </View>
                    </View>
                </View>

                <View>
                    <Text style={styles.header}>Comments</Text>
                    <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                    <View style= {styles.line}/>
                    
                    <View style={styles.addPadding}>
                        {/**The comment section */}
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
                                    selectedValue = {this.state.source}
                                    onValueChange={ (method) => { this.setState({source:method})} }
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
                                <Text style={{color:'red'}, styles.normalText}>*</Text>
                            </Text>

                            <View>
                                <Picker
                                    placeholder='Choose Method'
                                    mode={'dropdown'}
                                    note={false}
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.commentsPicker}
                                    selectedValue = {this.state.paymentMethod}
                                    onValueChange={ (itemValue) => this.setState({paymentMethod:itemValue}) }
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

                        {/**Where the deadlines need to be inserted */}
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
                            this.handleSubmit()
                        }}/>
                        {/*<Button title={'Print Forum'} color='red' onPress={ ()=>{
                            this.props.reset()
                        }}/>*/}
                    </View>
                </View>
            </ScrollView>
        )
    }
}

const styles = StyleSheet.create({
    textStyle: {
      fontSize:20,
    },
  
    centeredView: {
      flex: 1,
      justifyContent: "center",
      alignItems: "center",
      marginTop: 22
    },
    modalView: {
      margin: 20,
      backgroundColor: "white",
      borderRadius: 20,
      padding: 35,
      alignItems: "center",
      shadowColor: "#000",
      shadowOffset: {
        width: 0,
        height: 2
      },
      shadowOpacity: 0.25,
      shadowRadius: 3.84,
      elevation: 5
    },
    openButton: {
      backgroundColor: "red",
      borderRadius: 20,
      padding: 10,
      elevation: 2,
      width:'40%'
    },
    textStyle: {
      color: "white",
      fontWeight: "bold",
      textAlign: "center"
    },
    modalText: {
      marginBottom: 15,
      textAlign: "center"
    },
  
    header: {
      fontSize:20,
      textAlign:'center',
      paddingVertical:10,
      fontWeight:'bold',
    
    },
  
    normalText: {
      fontSize:20,
    },
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
  });

  
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
    getLeague: fetchLeague,
    onSubmit: submitTeam,
    saveTeamToState: saveTeamToState,
    reset: reset
  }
  
  const connectToStore = connect(
      mapStateToProps
  )
    // and that function returns the connected, wrapper component:
    const ConnectedComponent = connectToStore(IndividualRegister)
  
  export default connect(
    mapStateToProps,
    mapDispatchToProps
  )(IndividualRegister)