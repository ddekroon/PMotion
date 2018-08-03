import React, { Component } from 'react';
import { 
    Alert, 
    TouchableHighlight, 
    Button,
    Image,
    StyleSheet, 
    Text, 
    View, 
    WebView
} from 'react-native';
import styles from './styles.js';
import { getSportName, getSportLogo, getReadySet } from './sportFunctions.js';

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
        var sportLogo = getSportLogo(sportID);
        var readySetPlay = getReadySet(sportID);

        // console.warn("Sport: " + this.props.navigation.state.params.sportID);

        return (
            <View style={styles.container}>
                <View style={styles.logoContainer}>
                    <Image 
                        style={[styles.mainLogo, {maxHeight: '80%'}]}
                        source={sportLogo} 
                    />
                    <View style={styles.subLogo}>
                        <Image
                            style={[styles.mainLogo]}
                            source={readySetPlay}
                        />
                    </View>
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
