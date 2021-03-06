import React, { Component } from 'react';
import { View, WebView } from 'react-native';
import { showConnectionAlert } from './sportFunctions.js';
import styles from './styles.js';

export default class FieldStatus extends Component {

	static navigationOptions = ({ navigation }) => {
        return {
            title: 'Perpetual Motion Sports App',
            headerStyle: {
                backgroundColor: navigation.getParam('headerColour'),
            },
            headerTintColor: '#fff',
        };
    };

    constructor(props) {
        super(props);

        this.state = {
            // loaded: false,
        }
    }

	render() {
		const { navigate } = this.props.navigation;
        // const isLoaded = this.state.loaded;

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    <WebView 
                        source={{uri: 'http://guelph.ca/seasonal/sports-field-status/'}}
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
