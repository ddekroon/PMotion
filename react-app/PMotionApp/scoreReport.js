import React, { Component } from 'react';
import { StyleSheet, Text, View, WebView } from 'react-native';
import styles from './styles.js';

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
		const sportID = this.props.navigation.state.params.sportID;

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/web-app/score-reporter/' + sportID}}
                        onError={ ()=> {
                                return(
                                    <View>
                                        <Text>Error occurred while loading the page...</Text>
                                    </View>
                                    );
                            }
                        }
                    />
                </View>
            </View>
		);
	}
}
