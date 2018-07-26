import React, { Component } from 'react';
import { StyleSheet, Text, View, WebView } from 'react-native';
import NavBar from 'react-native-navbar';

export default class ScoreReporter extends Component {

	static navigationOptions = {
        title: 'Perpetual Motion Mobile',
        headerStyle: {
            backgroundColor: '#de1219',
        },
        headerTintColor: '#fff',
    };
	render() {
		const { navigate } = this.props.navigation;
        // console.warn("Report score for sport: " + this.props.navigation.state.params.sportID);

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/web-app/score-reporter/' + this.props.navigation.state.params.sportID}}
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
