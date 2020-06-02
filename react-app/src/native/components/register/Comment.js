import React, {useState} from 'react'
import {TextInput, Text, View, Button, Alert} from 'react-native'
import {Picker, Icon} from 'native-base'
import {StyleSheet} from 'react-native'


export default class Comment extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            comment:'',
            chosen:'',
            chosen2:'',
        }
    }

    render() {
        return (
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
                                style={ styles.picker}
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
                                style={ styles.picker}
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
                <View style={styles.addPadding}>
                    <Button title={'Register'}/>
                    <Button title={'Print form?'}/>
                </View>

            </View>
        )
    }
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
        flex:1,
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
        flexDirection:'row',
        height:40,
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center'
    },
})