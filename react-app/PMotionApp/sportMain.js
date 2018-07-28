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
                <Text style={styles.mainHeader}>{sportName} Menu</Text>
                <TouchableHighlight
                    style={styles.button}
                    onPress={() =>
                        navigate('Scores', {sportID: sportID})
                    }
                >
                    <Text style={styles.buttonText}>Report Scores</Text>
                </TouchableHighlight>
                <TouchableHighlight
                    style={styles.button}
                    onPress={() =>
                        navigate('Schedule', {sportID: sportID})
                    }
                >
                    <Text style={styles.buttonText}>View Schedules</Text>
                </TouchableHighlight>
            </View>
        );
    }
};
