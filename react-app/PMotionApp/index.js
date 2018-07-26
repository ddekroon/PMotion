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

export default class Index extends Component {

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
                <Button
                    title="Ultimate"
                    // color="#ff5f4e" // Find better colour choice?
                    onPress={() =>
                        navigate('Sports', {sportID: 1})
                    }
                />
                <Button
                    title="Volleyball"
                    // color="#ff5f4e" // Find better colour choice?
                    onPress={() =>
                        navigate('Sports', {sportID: 2})
                    }
                />
                <Button
                    title="Football"
                    // color="#ff5f4e" // Find better colour choice?
                    onPress={() =>
                        navigate('Sports', {sportID: 3})
                    }
                />
                <Button
                    title="Soccer"
                    // color="#ff5f4e" // Find better colour choice?
                    onPress={() =>
                        navigate('Sports', {sportID: 4})
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
        justifyContent: 'space-around',
    },
    webStyle: {
        flex: 1,
    },
    navBar: {
        backgroundColor: '#de1219',
        // topBarElevationShadowEnabled: true,
    },
});
