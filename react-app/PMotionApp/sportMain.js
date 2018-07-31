import React, { Component } from 'react';
import { 
    Alert, 
    TouchableHighlight, 
    Button,
    StyleSheet, 
    Text, 
    View, 
    WebView
} from 'react-native';
import styles from './styles.js';
import { getSportName } from './sportFunctions.js';

export default class SportMain extends Component {

    _onPressButton() {
        Alert.alert('Button pressed!')
    };
    static navigationOptions = {
        title: 'Perpetual Motion Mobile',
        headerStyle: {
            backgroundColor: '#de1219',
        },
        headerTintColor: '#fff',
    };

    render() {
        const { navigate } = this.props.navigation;
        var sportID = this.props.navigation.state.params.sportID;
        var sportName = getSportName(sportID);
        // console.warn("Sport: " + this.props.navigation.state.params.sportID);

        return (
            <View style={styles.container}>
                <View style={styles.header}>
                    <View style={{height: '15%'}}></View>
                    <Text style={styles.mainHeader}>{sportName} Menu</Text>
                    <Text style={styles.mainText}>Please select an option</Text>
                </View>

                <View style={styles.sportsContent}>
                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainerSingle}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Scores', {sportID: sportID})
                                }
                            >
                                <Text style={styles.buttonText}>REPORT SCORES</Text>
                            </TouchableHighlight>
                        </View>
                    </View>

                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainerSingle}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Schedule', {sportID: sportID})
                                }
                            >
                                <Text style={styles.buttonText}>VIEW SCHEDULES</Text>
                            </TouchableHighlight>
                        </View>
                    </View>
                </View>
            </View>
        );
    }
};
