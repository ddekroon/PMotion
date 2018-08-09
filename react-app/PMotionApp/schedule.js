import React, { Component } from 'react';
import { View, WebView } from 'react-native';
import { getSportName, showConnectionAlert } from './sportFunctions.js';
import styles from './styles.js';

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
            // loaded: false,
        }
    }

	render() {
		const { navigate } = this.props.navigation;
		const sportID = this.props.navigation.state.params.sportID;
        // const isLoaded = this.state.loaded;
        let sportName = getSportName(sportID);

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        // source={{uri: 'http://data.perpetualmotion.org/allSports/schedule.php?leagueID=1610'}}
                        source={{uri: 'http://perpetualmotion.org/' + sportName + '/schedules-standings#'}}
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
