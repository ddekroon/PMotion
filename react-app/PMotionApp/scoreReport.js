import React, { Component } from 'react';
import { View, WebView } from 'react-native';
import { showConnectionAlert } from './sportFunctions.js';
import styles from './styles.js';

export default class ScoreReporter extends Component {

	static navigationOptions = ({ navigation }) => {
        return {
            title: 'Perpetual Motion Sports App',
            headerStyle: {
                backgroundColor: navigation.getParam('headerColour'),
            },
            headerTintColor: '#fff',
        };
    };

	render() {
		const { navigate } = this.props.navigation;
		const sportID = this.props.navigation.state.params.sportID;

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/web-app/score-reporter/' + sportID}}
                        renderError={ ()=> {
                                return(
                                    showConnectionAlert()
                                    );
                            }
                        }
                    />
                </View>
            </View>
		);
	}
}
