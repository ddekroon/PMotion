import React, { Component } from 'react';
import { Alert, Button, StyleSheet, Text, View, WebView } from 'react-native';
import NavBar from 'react-native-navbar';

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
        // console.warn("Schedule for sport: " + this.props.navigation.state.params.sportID);

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/allSports/schedule.php?leagueID=1610'}}
                        style={{flex: 1}}
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

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
        alignItems: 'center',
        justifyContent: 'center',
    },
    webStyle: {
        flex: 1,
    },
    navBar: {
        backgroundColor: '#de1219',
        // topBarElevationShadowEnabled: true,
    },
});
