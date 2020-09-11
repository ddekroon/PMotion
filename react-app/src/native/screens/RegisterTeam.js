//Expects a sport id given as 1-4 under the 'sport' props tag

/** also expects this to be a prop given named as user (this is the team capt)
ex: const user = {
        user:'imckechn',
        FN:'Ian',
        LN:'McKechnie',
        email:'imckechn@uoguelph.ca',
        phone:'9056915041',
        gender:'Male',
    }
*/

import React from 'react'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import {Icon, Picker, Container, Content, Card } from 'native-base'
import { fetchLeague } from '../../actions/leagues' //Gets the leagues from the web.
import DateTimeHelpers from '../../utils/datetimehelpers'
import {TextInput, Text, View, Button} from 'react-native'
import {StyleSheet} from 'react-native'
import AddingTeamMembers from '../components/register/TeamRegisterNewTeammate'
import { submitTeam } from '../../actions/teams'
import ToastHelpers from '../../utils/toasthelpers'
import ValidationHelpers from '../../utils/validationhelpers'
import Colors from '../../../native-base-theme/variables/commonColor';
import { getTeamMembers } from '../../utils/teamMembersForNewTeamRegistration'
import {
    saveTeamToState,
    reset
} from '../../actions/teams'

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
            league: '',
            teamName: '',
            count:0,
            jsonPlayers:[]
        }

        //If data gets passed 
        if (this.props.userData) {  
            this.state = {
                FN:this.props.userData.FN,
                LN:this.props.userData.LN,
                email:this.props.userData.email,
                phone:this.props.userData.phone,
                gender:this.props.userData.gender
            }
        } else {
            this.state = {
                FN:'',
                LN:'',
                email:'',
                phone:'',
                gender:''
            }
        }

        //this has to be at the bottom for the state to stay equal to the props
        if (this.props.route?.params?.team?.league) {
            this.state = {
                league:props.route.params.team.league,
                teamName:props.route.params.team.name
            }
        }
    } 

    

    //When a new players info get changed, the update gets put into the state here
    update = (newJson) => {
        let arr = this.state.jsonPlayers
        arr[newJson.index] = newJson

        this.setState({jsonPlayers:arr})
    }

    //When the user changes their league choice, it gets updated in the state here
    updateLeague = (league) => {
        this.setState({ league: league })
    }

    //When submitting the forum, this checks that all the required fields are filled out
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

        if ( !ValidationHelpers.isValidEmail(this.state.email)) {
            str += ('\n- Team Captain email is invalid')
            noError = false
        }
        
        //Dave said we didnt need non-team captain contact info but I am leaving it in a comment just incase it changes
        /*for (player in this.state.jsonPlayers) { 

            if (this.state.jsonPlayers[player].email) {
                if ( !ValidationHelpers.isValidEmail(this.state.jsonPlayers[player].email) ) {
                    noError = false
                    str += ('\n- A player email is invalid')
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
        }*/

        if (noError) {
            return false

        } else {
            ToastHelpers.showToast(null, str)
            return true
        }
    }

    //This submits the forum to the server (Takes everything in the state and save it)
    handleSubmit = (seasons) => {
        if (this.checkAnswers()) return;
        
        //Creating the object that will be uploaded to redux/the server
        let obj = getTeamMembers(this.state.jsonPlayers)    //This initializes the object and populates all the team memeber elements of the object
        obj.action = 'register'
        obj.oldTeamID = '/' //Not sure why this is a /, but it was in the team i submitted, this can be corrected obviously.

        //Get the league ID
        let league;

        seasons[this.props.route?.params?.sport?this.props.route.params.sport:1][0].leagues.map( (curLeague) => {
            if (this.state.league == curLeague.name) {
                obj.leagueId = curLeague.id;
                league  = curLeague //Need this later 
                return;
            }
        })

        obj.teamName = this.state.teamName
        obj.wins = 0
        obj.loses = 0
        obj.ties= 0
        obj.sportID = this.props.route?.params?.sport?this.props.route.params.sport:1   //Same as bellow where I get the leagues

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
        
        
        obj.capFirstName = this.state.FN
        obj.capLastName = this.state.LN
        obj.capEmail = this.state.email
        obj.capGender = this.state.gender
        obj.capPhoneNumber = this.state.phone

        obj.teamComments = this.state.comment
        obj.capHowHeardMethod = this.state.hearMethod
        obj.capHowHeardcapHowHeardOtherMethodMethod = this.state.hearMethod   //Not sure that this was for, just in the object sent to the server when I made a test team
        obj.teamPaymentMethod = this.state.paymentMethod
        obj.submit = 'register'

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
        
        this.props.saveTeamToState(obj)

        //  To submit 
        const {onSubmit} = this.props
        onSubmit(obj).catch(e => {
            console.log("Encountered an error submitting the data")
        })
    }

    render() {

        let counter = 0;
        
        const {
            loading,
            seasons
        } = this.props

        return (
            <Container>
                <Content>
                    <Card style={{paddingLeft:10}}>
                        <View style={styles.addPadding}>
                            <View style={styles.setHorizontal}>

                                {/**Where the user chooses the league they want their team to play in */}
                                <Text style={styles.normalText}>League
                                    <Text style={styles.normalText, {color:Colors.brandSecondary} }>*</Text>
                                </Text>
                                <Picker
                                    placeholder="League"
                                    note={false}
                                    mode="dropdown"
                                    iosIcon={<Icon name="arrow-down" />}
                                    selectedValue = {this.state.league}
                                    onValueChange={this.updateLeague}
                                >

                                    <Picker.Item key={0} label={'League'} value={''} />
                                    {seasons[this.props.route?.params?.sport?this.props.route.params.sport:1][0].leagues.map(curLeague => {
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
                                {/**Where the user chooses their team name */}
                                <Text style={styles.normalText, {paddingRight:5}}>Team Name
                                    <Text style={styles.normalText, {color:Colors.brandSecondary} }>*</Text>
                                </Text>

                                <TextInput 
                                    style={styles.textInput}
                                    onChangeText={(val) => this.setState({teamName:val})} 
                                    value={this.state.teamName}
                                />
                                
                            </View>
                        </View>
                    </Card>

                    <Card style={{paddingLeft:10}}>
                        <View style={styles.padding}>
                            <Text style={{fontWeight:'bold'}}>Team Captain</Text>
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
                                    onChangeText={ (email) => this.setState({ email }) }
                                    style={styles.FillIn}
                                />
                            </View>
                            
                            <View style={styles.floatingBox}>
                                <Text style={styles.text}>Phone Number     </Text>
                                <TextInput  //Phone Number
                                    placeholder={'Phone Number'}
                                    value={this.state.phone}
                                    autoComplete={'tel'}
                                    keyboardType={'number-pad'}
                                    multiline={false}
                                    onChangeText={ (phone) => this.setState({ phone })}
                                    style={styles.FillIn}
                                />
                            </View>
                            
                            <View style={styles.floatingBox, {paddingBottom:5, flexDirection:'column', justifyContent: 'center', alignItems:'center'}}>
                                <Text style={styles.text}>Gender</Text>
                                <Picker
                                    placeholder='Gender'
                                    mode="dropdown"
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.picker}
                                    selectedValue = {this.state.gender}
                                    onValueChange={ (gender) => this.setState({gender:gender}) }
                                >
                                    <Picker.Item label='Gender' value='Gender' key={0} />
                                    <Picker.Item label="Male" value="Male" key={1} />
                                    <Picker.Item label="Female" value="Female" key={2} />
                                </Picker>
                            </View>
                        </View> 
                    </Card>

                    <Card style={{paddingLeft:10}}>
                        <Text style={styles.header}>Player Information</Text>
                        <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                        <View style= {styles.line}/>

                        {/**The loop that shows all the individual user forums */}
                        <View style={styles.stack}>
                            <View style={styles.stack}>
                                {counter = -1, this.state.jsonPlayers?this.state.jsonPlayers.map( (elem) => {
                                    counter++
                                    return (
                                        <Card key={counter}>
                                            <View style={styles.padding} key={counter}>
                                                <AddingTeamMembers json={elem} func={this.update}/>
                                            </View>
                                        </Card>
                                    )
                                }):null}
                            </View>
                            
                            {/**Add a new player */}
                            <View style={styles.setHorizontal}>
                                <Button style={styles.botButton} title={'Add Player'} onPress={() => {

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
                                        obj.gender = ''
                                        obj.phone = ''
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
                    </Card>

                    <Card style={{paddingLeft:10}}>
                        <Text style={styles.header}>Comments</Text>
                        <Text style = {styles.subHeading}>Comments, notes, player needs, etc. (limit 1000 characters).</Text>
                        <View style= {styles.line}/>
                        
                        <View style={styles.addPadding}>
                            <View style={ [styles.setHorizontal, styles.addPadding, styles.commentView]}>
                                <View style={{paddingTop:10}}/>
                                <Text style={styles.normalText}>Comments </Text>
                                <TextInput
                                    style={styles.textInput}
                                    placeHolder={'Comment here...'}
                                    multiLine={true}
                                    onChangeText= { (comment) => this.setState( {comment})}
                                    value={this.state.comment}                    
                                />
                            </View>
                            
                            {/**Where the user tells us hwo they heard about Perpetual Motion */}
                            <View style={styles.addPadding, {justifyContent:'center', alignItems:'center' }}>
                                <Text style={styles.normalText}>How did you hear about us?</Text>
                                <View>
                                    <Picker
                                        placeholder='Choose Method'
                                        mode="dropdown"
                                        iosIcon={<Icon name="arrow-down"/>}
                                        style={ styles.commentsPicker}
                                        selectedValue = {this.state.hearMethod}
                                        onValueChange={ (method) => { this.setState({hearMethod:method})} }
                                    >
                                        <Picker.Item label="Choose Method" value='' key={0} />
                                        <Picker.Item label="Google/Internet Search" value={1} key={1} />
                                        <Picker.Item label="Facebook Page" value={2} key={2} />
                                        <Picker.Item label="Kijiji Ad" value={3} key={3} />
                                        <Picker.Item label="Returning Player" value={4} key={4} />
                                        <Picker.Item label="From a Friend" value={5} key={5} />
                                        <Picker.Item label="Restaurant Ad" value={6} key={6} />
                                        <Picker.Item label="The Guelph Community Guide" value='The Guelph Community Guide' key={7} />
                                        <Picker.Item label="Other" value={8} key={8} />
                                        
                                    </Picker>
                                </View>
                            </View>
                        </View>
                    </Card>

                    <Card style={{paddingLeft:10}}>
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
                                        <Picker.Item label={"I will send an money email transfer to dave@perpetualmotion.org"} value={1} key={1} />
                                        <Picker.Item label={"I will mail a cheque to the Perpetual Motion head office"} value={2} key={2} />
                                        <Picker.Item label={"I will bring a cash/cheque to the Perpetual Motion head office"} value={3} key={3} />
                                        <Picker.Item label={"I will bring cash/cheque to registration night"} value={4} key={4} />
                                        
                                    </Picker>
                                </View>
                            </View>
                        </View>
                    </Card>
                    
                    <Card style={{paddingLeft:10}}>
                        {/**Where we need to add server calls to get the registration due dates*/}
                        <Text style={styles.header}>Registration Due By</Text>
                        <View style= {styles.line}/>
                        <View style={styles.addPadding}>
                            <Text style={ [styles.normalText, {fontWeight:'bold'}]}>Spring League</Text>

                            <Text style={styles.normalText}>Ultimate Frisbee
                                <Text style={styles.dates}>    *Insert date Here*</Text>
                            </Text>

                            <Text style={styles.normalText}>Beach Volleyball
                                <Text style={styles.dates}>    *Insert date Here*</Text>
                            </Text>

                            <Text style={styles.normalText}>Flag Football
                                <Text style={styles.dates}>    *Insert date Here*</Text>
                            </Text>

                            <Text style={styles.normalText}>Soccer
                                <Text style={styles.dates}>    *Insert date Here*</Text>
                            </Text>

                        </View>

                        <Text style={styles.header}>Register</Text>
                        <Text style = {styles.subHeading}>Submit your group registration to the convenor.</Text>
                        <View style= {styles.line}/>

                        <View style={styles.addPadding, {justifyContent:'space-between', flexDirection:'row'}}>
                            <Button title={'register (Submit)'} color='red' onPress={() => {
                                this.handleSubmit(seasons)
                            }}/>
                        </View>
                    </Card>
                </Content>
            </Container>
        )
    }
}

const styles = StyleSheet.create({
    header: {
        fontWeight:'bold',
        paddingBottom:5
    },

    subHeading: {
        color: '#474747',
        paddingBottom:5
    },

    line: {
        borderTopColor:'black',
        borderTopWidth:1,
        paddingBottom:15
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
        borderBottomColor:Colors.brandSecondary,
        width:'75%'
    },

    normalText: {
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
        borderBottomColor:Colors.brandSecondary,
        borderBottomWidth: StyleSheet.hairlineWidth,
        width:200,
    },

    text: {
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
    },

    dates: {
        color:Colors.brandSecondary
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

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RegisterTeam)