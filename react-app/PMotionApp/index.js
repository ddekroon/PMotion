import React, { Component } from 'react';
import { 
    Alert, 
    Button,
    Image,
    StyleSheet, 
    TouchableHighlight, 
    Text, 
    View, 
    WebView
} from 'react-native';
import NavBar from 'react-native-navbar';
import App from './App.js';
import styles from './styles.js';

export default class Index extends Component {

    // color="#ff5f4e" // Find better colour choice?

    _onPressButton() {
        Alert.alert('Button pressed!')
    };
    static navigationOptions = {
        title: 'Perpetual Motion Mobile',
        headerStyle: {
            backgroundColor: '#de1219',
        },
        headerTintColor: '#fff',
    };
    render() {
        const { navigate } = this.props.navigation;
        return (
            <View style={styles.container}>
                <View style={styles.logoContainer}>
                    <Image 
                        style={styles.logo}
                        resizeMode="contain"
                        source={require('./img/Perpetualmotionlogo2.png')} 
                    />
                </View>
                <View style={styles.header}>
                    <Text style={styles.mainHeader}>Select your sport</Text>
                </View>
                <View style={styles.content}>
                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Sports', {sportID: 1})
                                }
                            >
                                <Text style={styles.buttonText}>ULTIMATE</Text>
                            </TouchableHighlight>
                        </View>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Sports', {sportID: 2})
                                }
                            >
                                <Text style={styles.buttonText}>VOLLEYBALL</Text>
                            </TouchableHighlight>
                        </View>
                    </View>
                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Sports', {sportID: 3})
                                }
                            >
                                <Text style={styles.buttonText}>FOOTBALL</Text>
                            </TouchableHighlight>
                        </View>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={styles.button}
                                onPress={() =>
                                    navigate('Sports', {sportID: 4})
                                }
                            >
                                <Text style={styles.buttonText}>SOCCER</Text>
                            </TouchableHighlight>
                        </View>
                    </View>
                </View>
            </View>
        );
    }
}

const titleConfig = {
            title: 'Perpetual Motion Mobile',
            style: {color: '#fff'}
};
