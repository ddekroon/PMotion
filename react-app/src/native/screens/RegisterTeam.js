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

//Expects a sport id given as 1-4 under the 'sport' props tag

/** This is the object im expecting to be passed,
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
        onSubmit: PropTypes.func.isRequired
    }
  
    constructor(props) {
        super(props)
        this.state = {
            comment:'',
            chosen:'',
            chosen2:'',
            players:[],
            teamName:'',
            count:0,
            imgArr:[]
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
        let obj = JSON.parse(newJson)

        let arr = this.state.imgArr
        arr[obj.index] = newJson

        this.setState({imgArr:arr})
    }

    handleToScreen = () => {
        this.setState({ input: ''})
    }

    state = {league: ''}
    updateLeague = (league) => {
         this.setState({ league: league })
    }

    handleSubmit = () => {
        //Do this so the last player is the team capt/ the person filling in the forum
        obj = new Object
        obj.fn = this.state.FN
        obj.ln = this.state.LN
        obj.email = this.state.email
        obj.sex = this.state.sex
        obj.key = this.state.imgArr.length

        temp = this.state.imgArr
        temp/push(JSON.stringify(obj))

        this.setState({imgArr:temp})
        
        //Submit it (POST)
        console.log("Submitted!")
        const {onSubmit} = this.props
        onSubmit().catch(e => {
            console.log("Encountered an error submitting the data")
        })
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
                    <Text style={styles.normalText}>League</Text>
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
                    <Text style={styles.normalText}>Team Name</Text>
                    <TextInput style={styles.textInput} onChangeText={(val) => this.setState({teamName:val})} value={this.state.teamName}/>
                    </View>
                </View>

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
                            {counter = -1, this.state.imgArr?this.state.imgArr.map( (elem) => {
                                counter++
                                return (<View style={styles.padding} key={counter}>
                                        <AddingTeamMembers json={elem} func={this.update}/>
                                    </View>)
                            }):null}
                        </View>

                        <View style={styles.setHorizontal}>
                            <Button style={styles.botButton} title={'Add Player'} onPress={() => {
                                //Check for max list lenght
                                

                                if (this.state.imgArr && this.state.imgArr.length == 14) {
                                    alert("Cannot have a team size greater than 15")
                                } else {
                                    
                                    //create the object and set its index to the current count (then update the count)
                                    let obj = new Object
                                    obj.index = this.state.count?this.state.count:0
                                    this.setState({count: obj.index + 1})

                                    obj.fn = ''
                                    obj.ln = ''
                                    obj.email = ''
                                    obj.sex = ''
                                    obj.key = obj.index

                                    let str = JSON.stringify(obj)
                                    let arr = this.state.imgArr?this.state.imgArr:[]
                                    arr.push(str)
                                    this.setState({imgArr:arr})
                                }
                            }}/>

                            <Button title={'Remove Player'} style={styles.botButton} onPress={() => {
                                let arr = this.state.imgArr
                                arr.pop()

                                this.setState({imgArr:arr})
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
                                    selectedValue = {this.state.chosen}
                                    onValueChange={ (method) => { this.setState({chosen:method})} }
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
                                    selectedValue = {this.state.chosen2}
                                    onValueChange={ (itemValue, itemIndex) => this.setState({chosen2:itemValue}) }
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
                            console.log("HERE")
                            this.handleSubmit()
                        }}/>
                        <Button title={'Print Forum'} color='red'/>
                        <Button title={'Save Details'} color='red'/>
                    </View>
                </View>
                <Button title={"Get full state"} onPress={() => console.log(JSON.stringify(this.state))}/>
            </ScrollView>
        )
    }
}

//The picker function for chosing the sex of the player
export const dropDownSex = (sex) => {
    let chosen = ''

    if (sex) {
        chosen = (sex)
    } 
    
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
  TeamSubmisson: state.TeamSubmisson
})

const mapDispatchToProps = { 
  getLeague: fetchLeague,
  onSubmit: submitTeam
}

const connectToStore = connect(
    mapStateToProps
)
  // and that function returns the connected, wrapper component:
  const ConnectedComponent = connectToStore(RegisterTeam)

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RegisterTeam)