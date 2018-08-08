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

    constructor(props) {
        super(props);

        this.state = {
            loaded: false,
        }
    }

	render() {
		const { navigate } = this.props.navigation;
		const sportID = this.props.navigation.state.params.sportID;
        const isLoaded = this.state.loaded;

		return (
			<View style={{flex: 1}}>
                {isLoaded ? (null) : (
                    <View>
                        <Text style={{textAlign: 'center'}}>If page doesn't appear within a few seconds, please check internet connection</Text>
                    </View>
                )}
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://data.perpetualmotion.org/web-app/score-reporter/' + sportID}}
                        onLoad={ ()=> {
                                this.setState({ loaded: true });
                            }
                        }
                        renderError={ ()=> {
                                return(
                                    <View>
                                        <Text>Error occurred while loading the page... Please check your internet connection</Text>
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
