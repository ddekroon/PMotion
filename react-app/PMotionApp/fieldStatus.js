import React, { Component } from 'react';
import { StyleSheet, Text, View, WebView } from 'react-native';
import styles from './styles.js';

export default class FieldStatus extends Component {

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
        const isLoaded = this.state.loaded;

		return (
			<View style={{flex: 1}}>
                <View style={styles.webStyle}>
                    {isLoaded ? (null) : (
                        <View>
                            <Text>If page doesn't appear within a few seconds, please check internet connection</Text>
                        </View>
                    )}
                    <WebView 
                        source={{uri: 'http://guelph.ca/seasonal/sports-field-status/'}}
                        onLoadEnd={ ()=> {
                                this.setState({ loaded: true });
                            }
                        }
                        renderError={ ()=> {
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
