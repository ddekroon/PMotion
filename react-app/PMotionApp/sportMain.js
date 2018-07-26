import React, { Component } from 'react';
import { 
    Alert, 
    TouchableHighlight, 
    Button,
    StyleSheet, 
    Text, 
    View, 
    WebView
} from 'react-native';
import NavBar from 'react-native-navbar';
import App from './App.js';

export default class SportMain extends Component {

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
        // console.warn("Sport: " + this.props.navigation.state.params.sportID);

        return (
            <View style={styles.container}>
                <Button
                    title="Report Scores"
                    // color="#ff5f4e" // Find better colour choice?
                    onPress={() =>
                        navigate('Scores', {sportID: this.props.navigation.state.params.sportID})
                    }
                />
                <Button
                    title="View Schedule"
                    onPress={() =>
                        navigate('Schedule', {sportID: this.props.navigation.state.params.sportID})
                    }
                />
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
        justifyContent: 'space-evenly',
    },
    webStyle: {
        flex: 1,
    },
    navBar: {
        backgroundColor: '#de1219',
        // topBarElevationShadowEnabled: true,
    },
});
