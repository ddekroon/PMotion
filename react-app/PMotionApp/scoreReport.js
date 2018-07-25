import React, { Component } from 'react';
import { StyleSheet, Text, View, WebView } from 'react-native';
import NavBar from 'react-native-navbar';

export default class Schedule extends Component {

	render() {
		return (
			<View style={{flex: 1, marginTop: 20}}>
                <View>
                    <NavBar title={titleConfig} style={styles.navBar} />
                </View>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/web-app/score-reporter/4'}}
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
