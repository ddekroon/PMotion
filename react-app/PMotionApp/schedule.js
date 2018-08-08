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

        let sportName = getSportName(sportID);

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    {isLoaded ? (null) : (
                        <View>
                            <Text>If page doesn't appear within a few seconds, please check internet connection</Text>
                        </View>
                    )}
                    <WebView 
                        // source={{uri: 'http://data.perpetualmotion.org/allSports/schedule.php?leagueID=1610'}}
                        source={{uri: 'http://perpetualmotion.org/' + sportName + '/schedules-standings#'}}
                        onLoadEnd={ ()=> {
                                this.setState({ loaded: true });
                            }
                        }
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
