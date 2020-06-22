import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { fetchLeague } from '../../actions/leagues' //Gets the leagues from the web.
import {
    Text, 
    View, 
    ScrollView,
    StyleSheet,
    Modal,
    TouchableHighlight,
    Button,
    TextInput
} from 'react-native'
import {Picker, Icon} from 'native-base'
import PickTeam from '../components/register/Teams'
import PickLeagues from '../components/register/ChooseLeague'
import {AddingTeamMembers} from '../components/register/TeamMember'
import RegisterTeam from '../components/register/RegisterTeam'
import { submitTeam } from '../../actions/teams'

//This is the base template im using for team register
const user = {
    user:'imckechn',
    FN:'Ian',
    LN:'McKechnie',
    email:'imckechn@uoguelph.ca',
    phone:'9056915041',
    sex:'Male',
}

const Ultimate = {
    "name":"Ultimate",
    "id":1
}

const Volleyball = {
    "name":"Volleyball",
    "id":2
}

const Football = {
    "name":"Football",
    "id":3
}

const Soccer = {
    "name":"Soccer",
    "id":4
}

class IndividualRegister extends React.Component {
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
            modalVisible: false,
            comment:'',
            chosen:'',
            chosen2:'',
            players:[],
            teamName:'',
            count:0,
            imgArr:[]
        }
    }

    setModalVisible = (visible) => {
        this.setState({ modalVisible: visible });
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
        console.log("Submitted!")
        const {onSubmit} = this.props
        onSubmit().catch(e => {
            console.log("Encountered an error submitting the data")
        })
    }

    render() {
        let counter = 0
        const { modalVisible } = this.state

        if (!seasons) console.log("Error loading seasons")

        const {
            loading,
            seasons
        } = this.props
        
        return(
            <ScrollView>
                <Text style={styles.header}>Registration</Text>
                <View style={{alignItems:'center', justifyContent:'center'}}>
                    <PickTeam sports={ [Ultimate, Football, Volleyball, Soccer]}/>
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
                    
                    <Text style={styles.normalText}>Prefered League
                        <Text style = {styles.normalText, {color:'red'}}>*</Text>
                    </Text>   
                    <PickLeagues sport={4} style={{paddingBottom:10}}/>

                    <Text style={styles.normalText}>Secondary Choice</Text>   
                    <PickLeagues sport={4} style={{paddingBottom:10}}/>

                    <Text style={styles.normalText}>Tertiary Choice</Text>   
                    <PickLeagues sport={4} style={{paddingBottom:10}}/>
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
                                
                                if (this.state.imgArr && this.state.imgArr.length == 15) {
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
  )(IndividualRegister)