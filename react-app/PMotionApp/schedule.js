import React, { Component } from 'react';
import { Alert, Button, StyleSheet, Text, View, WebView } from 'react-native';
import styles from './styles.js';
import { getSportName } from './sportFunctions.js';

export default class Schedule extends Component {

	static navigationOptions = {
        title: 'Perpetual Motion Mobile',
        headerStyle: {
            backgroundColor: '#de1219',
        },
        headerTintColor: '#fff',
    };
	render() {
		const { navigate } = this.props.navigation;
		const sportID = this.props.navigation.state.params.sportID;
        // console.warn("Schedule for sport: " + this.props.navigation.state.params.sportID);

        let sportName = getSportName(sportID);

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        // source={{uri: 'http://data.perpetualmotion.org/allSports/schedule.php?leagueID=1610'}}
                        source={{uri: 'http://perpetualmotion.org/' + sportName + '/schedules-standings#'}}
                        scalesPageToFit={true}
                    />
                </View>
            </View>
		);
	}

	
}

const titleConfig = {
            title: 'Perpetual Motion Mobile',
            style: {color: '#fff'}
};
