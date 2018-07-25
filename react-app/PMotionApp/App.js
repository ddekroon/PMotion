import React, { Component } from 'react';
import { 
    Alert, 
    TouchableHighlight, 
    StyleSheet, 
    Text, 
    View, 
    WebView
} from 'react-native';
import NavBar from 'react-native-navbar';

export default class Index extends Component {

    _onPressButton() {
        Alert.alert('Button pressed!')
    }
    render() {

        return (
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
