import React from 'react'
import {View, ScrollView, Text, StyleSheet, Button, Clipboard, TextInput, Image} from 'react-native'
import { Picker, Icon, Container, Header, Content, Card, CardItem, Body} from 'native-base'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import { submitTeam } from '../../actions/teams'
import {
    saveTeamToState,
    reset
} from '../../actions/teams'
import Colors from '../../../native-base-theme/variables/commonColor';

class Profile extends React.Component {
    static propTypes = {
        onSubmit: PropTypes.func.isRequired,
        saveTeamToState: PropTypes.func.isRequired,
    }

    constructor(props) {
        super(props)
        this.state = {
            Fn:'',  //Will need to populate this from the redux state once you can get info from the user
            Ln:'',
            email:'',
            phone:'',
            gender:'',
            userID:123456   //This is a stub 
        }
    }

    updateGender(newGender) {
        this.setState({gender:newGender})
    }
        
    writeToClipboard = async () => {
        await Clipboard.setString('https://data.perpetualmotion.org/web-app/download-ics/' + this.state.userID);
        alert('Copied to Clipboard!\nNow paste this into your calendar app.');
    }

    submit() { 
        
        if (this.user.FN != this.state.Fn) {
            this.user.FN = this.state.Fn
        }

        if (this.user.LN != this.state.Ln) {
            this.user.LN = this.state.Ln
        }

        if (this.user.email != this.state.email) {
            this.user.email = this.state.email
        }

        if (this.user.phone != this.state.phone) {
            this.user.phone = this.state.phone
        }

        if (this.user.gender != this.state.gender) {
            this.user.gender = this.state.gender
        }

        
        console.log("Submitted to state (Not actually since it's a stub rn")
        //this.props.saveTeamToState(user)

        //  To submit 
        /*const {onSubmit} = user
        onSubmit().catch(e => {
            console.log("Encountered an error submitting the data")
        })*/
    }

    render() {

        return (
            <Container>
                <Content>
                    <Card style={{paddingLeft:10}}>
                        <View>
                            <Text style={styles.header}>Hello
                                <Text style={styles.header, {color:Colors.brandSecondary}}> {this.state.Fn} {this.state.Ln}</Text>
                            </Text>
                            <Text style={{fontSize:25}}>Welcome to your profile</Text>
                        </View>
                    </Card>
                    <Card style={{paddingLeft:10}}>
                        <View style={{padding:10}}/>

                        <Text style={styles.header}>Calendar Integration</Text>
                        <View style={styles.line}/>
                        
                        <View style={{flexDirection:'row', justifyContent:'space-evenly', paddingTop:10}}>
                            <View style={styles.leftView}>
                                
                                <Text style={styles.subheading, {padding:2}}>
                                    <Image style={styles.infoImg} source={require('../../images/icons/info.png')} />
                                    Sync your scheduled matches to your personal calendar. Just paste the link below into Gmail, iCal, Outlook, etc. to sync your games.
                                </Text>
                            </View>

                            <View style={styles.rightView}>
                                <Text>https://data.perpetualmotion.org/web-app/download-ics/{this.state.userID}</Text>
                            </View>
                        </View>

                        <Button title={'Copy to clipboard'} onPress={this.writeToClipboard}/>
                        <View style={{paddingBottom:10}}/>
                    </Card>
                    <Card style={{paddingLeft:10}}>
                        <Text style={styles.header}>Edit Information</Text>
                        <View style={styles.line}/>

                        <View style={styles.inputView}>
                            <Text style={styles.plainText}>First Name</Text>
                            <TextInput
                                value={this.state.Fn}
                                multiline={false}
                                autoCapitalize={'words'}
                                autoComplete={'name'}
                                onChangeText={(elem) => {this.setState({Fn:elem})}} 
                                style={styles.input}   
                            />
                        </View>

                        <View style={styles.inputView}>
                            <Text style={styles.plainText}>Last Name</Text>
                            <TextInput
                                value={this.state.Ln}
                                onChangeText={(elem) => {this.setState({Ln:elem})}} 
                                style={styles.input}   
                                autoCapitalize={'words'}
                                multiline={false}
                            />
                        </View>

                        <View style={styles.inputView}>
                            <Text style={styles.plainText}>Email</Text>
                            <TextInput
                                value={this.state.email}
                                onChangeText={(elem) => {this.setState({email:elem})}}   
                                style={styles.input} 
                                keyboardType={'email-address'}
                                multiline={false}
                                autoComplete={'email'}
                            />
                        </View>

                        <View style={styles.inputView}>
                            <Text style={styles.plainText}>Phone Number</Text>
                            <TextInput
                                value={this.state.phone}
                                onChangeText={(elem) => {this.setState({phone:elem})}}    
                                style={styles.input}
                                keyboardType={'number-pad'}
                                multiline={false}
                            />
                        </View>

                        <View style={styles.inputView}> 
                            <Text style={styles.plainText}>Gender</Text>
                            
                            <Picker
                                note={false}
                                placeholder={this.state.gender?this.state.gender:'Gender'}
                                onValueChange={ (gender) => {
                                    this.setState({gender:gender})
                                }}
                                selectedValue={this.state.gender}
                                mode="dropdown"
                                iosIcon={<Icon name="arrow-down" />}
                            >
                                <Picker.Item label='Male' value='Male' key={0}/>
                                <Picker.Item label='Female' value='Female' key={1}/>
                            </Picker>
                        </View>

                        <View style={{alignItems:'flex-end'}}>
                            <Button 
                                title={'Save'}
                                onPress={() => {
                                    this.submit()
                                }}
                            />
                        </View>
                        
                        <Text style={{fontSize:5}}>© Copyright 2020, Perpetual Motion</Text>                        
                    </Card>
                </Content>
            </Container>

        )
    }
}

const styles = StyleSheet.create({ 
    header: {
        fontWeight:'bold',
      },
    
    subHeading: {
        color: '#474747',
    },
    
    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
    },

    infoImg: {
        height:10,
        width:10
    },

    leftView: {
        width:'45%',
        flexWrap:'wrap',
        flexDirection:'row',
        borderWidth:1,
        borderColor:'#8d99ae',
        backgroundColor:Colors.brandInfo
    },

    rightView: {
        width:'45%',
        fontFamily:'Menlo',
        borderWidth:1,
        borderColor:'#8d99ae',
    },


    input: {
        borderBottomWidth:1,
        borderColor:'red',
        width:'100%',
    },

    plainText: {
        paddingRight:10,
    },

    inputView: {
        paddingBottom:15,
        paddingTop:15,
        flexDirection:'row',
    }
})

const mapStateToProps = state => ({
    TeamSubmisson: state.TeamSubmisson,
    user: state.currentUser || {}
  })

const mapDispatchToProps = { 
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
)(Profile)
