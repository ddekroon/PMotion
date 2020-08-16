import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import ToastHelpers from '../../utils/toasthelpers'
import ValidationHelpers from '../../utils/validationhelpers'
import { saveWaiverToState } from '../../actions/Waiver'

import { Text, View, StyleSheet, Switch, TextInput, Button, ScrollView} from 'react-native'
import {List, ListItem, Container, Header, Content, Card, CardItem, Body} from 'native-base'

class Waivers extends React.Component {

    static propTypes = {
        onFormSubmit: PropTypes.func.isRequired
    }
    
    constructor(props) {
        super(props)
        this.state= {
            checked:false,
            name:'',
            parentName:'',
            email:'',
            parentEmail:''
        }
    }
    

    submit() {

        console.log("props = " + JSON.stringify(this.props))
        
        if (!this.state.checked ) {
            ToastHelpers.showToast(null, 'Please accept the terms and conditions before submitting')
            return;
        }

        if (this.state.name == '') {
            ToastHelpers.showToast(null, 'Please fill in your name before submitting')
            return;
        }

        if (this.state.email == '') {
            ToastHelpers.showToast(null, 'Please fill in your email before submitting')
            return;
        }

        if ( !ValidationHelpers.isValidEmail(this.state.email) ) {
            ToastHelpers.showToast(null, 'Email is invalid')
            return;
        }

        if (this.state.parentEmail != '') {
            if ( !ValidationHelpers.isValidEmail(this.state.parentEmail) ) {
                ToastHelpers.showToast(null, 'Parents email is invalid')
                return;
            } 

            if (this.state.parentName== '') {
                ToastHelpers.showToast(null, 'Need a parent name')
                return;
            }
        }

        if (this.state.parentName != '') {
            if (this.state.parentEmail == '') {
                ToastHelpers.showToast(null, 'Need a parent email')
                return;
            }   
        }
        
        let obj = new Object
        
        obj.name = this.state.name
        obj.email = this.state.email
        obj.guardEmail = this.state.parentEmail
        obj.guardName = this.state.parentName

        let day = new Date().getDay()
        let month = new Date().getMonth()
        let year = new Date().getFullYear()

        obj.dateString = month + '/' + day + '/' + year
        obj.Submit = 'Submit'
        //Link : https://data.perpetualmotion.org/waiver.php?sportID=1

        const { onFormSubmit } = this.props
        onFormSubmit(obj).catch(e => {
            ToastHelpers.showToast(Enums.messageTypes.Error, e.message)
        })

        //this.props.addWaiver(Waiver)
        
    }

    getDay() {
        let date =  new Date().getDay() + ""
        let end 

        if (date == 1) {
            end = 'st'

        } else if (date == 2){
            end = 'nd'

        } else if (date == 3){
            end = 'rd'
        
        } else if (date == 21){
            end = 'st'

        } else if (date == 22){
            end = 'nd'

        } else if (date == 23){
            end = 'rd'
        
        } else if (date == 31){
            end = 'st'

        } else {
            end = 'th'
        }

        return date + end
    }

    getMonth() {
        let date =  new Date().getMonth()
        let month 

        if (date == 0){
            month = 'January'
        
        } else if (date ==  1){
            month = 'February'
        
        } else if (date == 2){
            month = 'March'

        } else if (date == 3){
            month = 'April'

        } else if (date == 4){
            month = 'May'

        } else if (date == 5){
            month = 'June'

        } else if (date == 6){
            month = 'July'
        
        } else if (date == 7){
            month = 'August'
        
        } else if (date == 8){
            month = 'September'
        
        } else if (date == 9){
            month = 'October'
        
        } else if (date == 10){
            month = 'November'
        
        } else if (date == 11){
            month = 'December'
        
        } else {
            month = 'This Month'
        }
        
        return month
    }

    getYear() {
        return new Date().getFullYear();
    }

    render() {

        return (
            <Container>
                <Content>
                    <Card>
                        <CardItem>
                            <Body>
                                <ScrollView>

                                    <View style={{justifyContent:'center', paddingBottom:20, width:'100%'}}>
                                        <Text style={{fontWeight:'bold', fontSize:40}}>Waiver</Text>
                                    </View>
                                    
                                    {/**Release of Liability Waiver */}
                                    <View style={{maxWidth:'100%'}}>
                                        <View style={{alignContent:'center'}}>
                                            <Text style={styles.c}>RELEASE OF LIABILITY, WAIVER OF CLAIMS</Text>
                                            <View stle={{paddingBottom:10}}/>
                                            <Text style={styles.d}>ASSUMPTION OF RISKS AND INDEMNITY AGREEMENT</Text>
                                            <Text style={styles.e}>By signing this document you will waive certain legal rights, including the right to sue.</Text>
                                            <Text style={styles.b, {fontWeight:'bold'}}>PLEASE READ CAREFULLY</Text>
                                        </View>
                                        
                                        <View>
                                            <View style={{paddingBottom:10}}/>
                                            <Text style={styles.e, {textDecorationLine:'underline', fontWeight:'bold'}}>AWARENESS AND ASSUMPTION OF RISK</Text>
                                            <View style={{paddingBottom:10}}/>
                                            <Text>I am aware that sports involves risks including risk of personal injury, death, property damage, expense and related loss, including loss of income. Included in these risks are negligence on the part of Perpetual Motion Sports & Entertainment Inc. (known as "Perpetual Motion"), its directors, officers, officials, shareholders, employees and volunteers, other participants and owners of the facilities where the activities occur (referred to in the rest of this agreement as PERPETUAL MOTION and OTHERS). I freely accept and fully assume all such risks and the possibility of personal injury, death, property damage, expense and related loss, including loss of income.</Text>
                                            <Text></Text>
                                            <Text>The novel coronavirus, COVID-19, has been declared a worldwide pandemic by the World Health Organization and COVID-19 is extremely contagious. PERPETUAL MOTION has put in place preventative measures to reduce the spread of COVID-19; however, I understand that PERPETUAL MOTION AND OTHERS cannot and does not guarantee that I will not become infected with COVID-19. Further, participating in any group activity and sports may significantly increase my risk of contracting COVID-19 and such exposure may result in temporary or permanent personal injury, illness, disability or death and I freely and voluntarily agree to assume all the foregoing risks. </Text>

                                            <View style={{paddingBottom:10}}/>
                                            <Text style={{fontWeight:'bold', textDecorationLine:'underline'}}>RELEASE OF LIABILITY, WAIVER OF CLAIMS, INDEMNITY AGREEMENT & PHOTO RELEASE</Text>
                                            <View style={{paddingBottom:10}}/>

                                            <Text>In consideration of PERPETUAL MOTION accepting my application to participate in this activity, I agree:</Text>

                                            <List>
                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>1. </Text>
                                                        This is a continuous waiver (current and future years) and I waive any and all claims that I may have in future against PERPETUAL MOTION and OTHERS.
                                                    </Text>
                                                </ListItem>

                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>2. </Text>
                                                        To release PERPETUAL MOTION and OTHERS from any and all liability for any personal injury, death, property damage, expense and related loss, including loss of income that I or my next of kin may suffer as a result of my participation in this activity, due to any cause whatsoever, including negligence, breach of contract or breach of any statutory duty of care.
                                                    </Text>
                                                </ListItem>

                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>3. </Text>
                                                        To hold harmless and indemnify PERPETUAL MOTION and OTHERS from any and all liability for any damage to property of, or personal injury to, any third party, resulting from my participation in this activity.
                                                    </Text>
                                                </ListItem>

                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>4. </Text>
                                                        That this agreement is binding on not only myself but my next if kin, heirs, executors, administrators and assigns.
                                                    </Text>
                                                </ListItem>

                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>5. </Text>
                                                        I grant permission to PERPETUAL MOTION AND OTHERS to photograph and/or record my image and/or voice on still or motion picture film and/or audio tape, and to use this material to promote PERPETUAL MOTION through the media of newsletters, websites, television, film, radio, print and/or display form. I waive any claim to remuneration for use of audio/visual materials used for these purposes. I understand that I may withdraw such consent at any time by contacting PERPETUAL MOTION. PERPETUAL MOTION will advise the implications of such withdrawal.
                                                    </Text>
                                                </ListItem>

                                                <ListItem>
                                                    <Text>
                                                        <Text style={{fontWeight:'bold'}}>6. </Text>
                                                        To FOREVER RELEASE AND INDEMNIFY PERPETUAL MOTION AND OTHERS relating to becoming exposed to or infected by COVID-19 which may result from the actions, omission or negligence of myself and others, including but not limited to PERPETUAL MOTION and other participants in activities and sports offered by the PERPETUAL MOTION.
                                                    </Text>
                                                </ListItem>

                                            </List>

                                            <View style={{paddingTop:15}}/>
                                            <Text>I HAVE READ THIS AGREEMENT AND UNDERSTAND IT. I AM AWARE THAT BY SIGNING THIS DOCUMENT I AM WAIVING CERTAIN RIGHTS WHICH I OR MY NEXT OF KIN, HEIRS, EXECUTORS, ADMINISTRATORS AND ASSIGNS MAY HAVE AGAINST PERPETUAL MOTION AND OTHERS. I WARRANT THAT AT THE TIME OF SIGNING, I AM PHYSICALLY FIT TO PARTICIPATE.</Text>
                                            <View style={{paddingTop:5}}/>
                                            <View style={{backgroundColor:'#d3d3d3', flexDirection:'row', width:'100%'}}>
                                                <Switch 
                                                    trackColor={{ false: "red", true: "green" }}
                                                    //thumbColor={this.state.checked ? "#f5dd4b" : "#f4f3f4"}
                                                    ios_backgroundColor="#3e3e3e"
                                                    onValueChange={() => this.setState({checked: !this.state.checked})}
                                                    value={this.state.checked}
                                                />
                                                <View style={{width:'80%'}}>
                                                    <Text>* By checking this box, I agree to all of the terms and conditions listed above.</Text>
                                                </View>
                                                
                                            </View>

                                            <View style={{paddingTop:15}}/>
                                            <View style={{flexDirection:'column', justifyContent:'space-between'}} >
                                                <Text>* Name:</Text>
                                                <TextInput
                                                    style={{
                                                        borderBottomWidth:1,
                                                        borderBottomColor:'red',
                                                        width:'100%'
                                                    }}
                                                    value={this.state.name}
                                                    onChangeText={(name) => this.setState({name:name})}
                                                />
                                                
                                            </View>
                                            
                                            <View style={{paddingTop:15}}/>
                                            <View style={{flexDirection:'column', justifyContent:'space-between'}} >
                                                
                                                <Text>Parent/Guardian's Name (if under 18):</Text>
                                                <TextInput
                                                    style={{
                                                        borderBottomWidth:1,
                                                        borderBottomColor:'red',
                                                        width:'100%'
                                                    }}
                                                    value={this.state.parentName}
                                                    onChangeText={(name) => this.setState({parentName:name})}
                                                />
                                            </View>

                                            <View style={{paddingTop:15}}/>
                                            <View style={{flexDirection:'column', justifyContent:'space-between'}} >
                                                <Text>* Email Address:</Text>
                                                <TextInput
                                                    style={{
                                                        borderBottomWidth:1,
                                                        borderBottomColor:'red',
                                                        width:'100%'
                                                    }}
                                                    value={this.state.email}
                                                    onChangeText={(email) => this.setState({email:email})}
                                                    keyboardType={'email-address'}
                                                />
                                                
                                            </View>

                                            <View style={{paddingTop:15}}/>
                                            <View style={{flexDirection:'column', justifyContent:'space-between'}} >
                                                
                                                <Text>Parent/Guardian's Email Address (if under 18):</Text>
                                                <TextInput
                                                    style={{
                                                        borderBottomWidth:1,
                                                        borderBottomColor:'red',
                                                        width:'100%'
                                                    }}
                                                    value={this.state.parentEmail}
                                                    onChangeText={(email) => this.setState({parentEmail:email})}
                                                    keyboardType={'email-address'}
                                                />
                                            </View>
                                        </View>

                                        <View style={{paddingTop:15}}/>

                                        <View>
                                            <View style={{paddingTop:10, paddingBottom:10, backgroundColor:'#d3d3d3'}}>
                                                <Text style={{fontStyle:'italic'}}>
                                                    <Text>Dated this </Text>
                                                    <Text style={{textDecorationLine:'underline'}}>{this.getDay()}</Text>
                                                    <Text> day of </Text>
                                                    <Text style={{textDecorationLine:'underline'}}>{this.getMonth()}</Text>
                                                    <Text> in the year </Text>
                                                    <Text style={{textDecorationLine:'underline'}}>{this.getYear()}</Text>
                                                    <Text>.</Text>
                                                </Text>
                                            </View>

                                            <View style={{flexDirection:'row', justifyContent:'space-between'}} >
                                                <Button
                                                    title={'Submit'}
                                                    onPress={ ()=> {
                                                        this.submit();
                                                    }}
                                                />

                                                <Button
                                                    title={'Show props'}
                                                    onPress={ ()=> {
                                                        console.log("props = " + JSON.stringify(this.props))
                                                    }}
                                                />
                                            </View>
                                        </View>
                                    </View>
                                </ScrollView>
                            </Body>
                        </CardItem>
                    </Card>
                </Content>
            </Container>
            
        )
    }
}

//Higher the letter, the larger the font
const styles = StyleSheet.create({
    a: {
        fontSize:40
    },

    b: {
        fontSize:30,
    },
    
    c: {
        fontSize:30,
    },

    d: {
        fontSize:20,
    },

    e: {
        fontSize:20,
    },

    f: {
        fontSize:15
    }
})


const mapStateToProps = state => ({
    theState: state
  })
  
  const mapDispatchToProps = {
    onFormSubmit: saveWaiverToState,
  }
  
  export default connect(
    mapStateToProps,
    mapDispatchToProps
  )(Waivers)