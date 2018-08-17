import React, { Component } from 'react';
import { 
    Alert, 
    Button,
    Image,
    Linking,
    StyleSheet, 
    TouchableHighlight, 
    Text, 
    View, 
    WebView
} from 'react-native';
import App from './App.js';
import { AppLoading, Asset, SplashScreen } from 'expo';
import { _onPressImage, getSportColour } from './sportFunctions.js';
import styles from './styles.js';

export default class Index extends Component {

    static navigationOptions = ({ navigation }) => {
        return {
            title: 'Perpetual Motion Sports App',
            headerStyle: {
                backgroundColor: '#de1219',
            },
            headerTintColor: '#fff',
        };
    };

    state = {
        isSplashReady: false,
        isAppReady: false,
    }

    render() {
        if (!this.state.isSplashReady) {
            return (
                <AppLoading
                    startAsync={this._cacheSplashResourcesAsync}
                    onFinish={() => this.setState({ isSplashReady: true })}
                    onError={console.warn}
                    autoHideSplash={false}
                />
            );
        }

        if (!this.state.isAppReady) {
            // return this._cacheResourcesAsync();
            return (
                <View style={{flex: 1, justifyContent: 'center', backgroundColor: '#fff'}}>
                    <Image
                        source={require('./img/splash.png')}
                        style={{resizeMode: 'contain', width: '100%'}}
                        onLoad={this._cacheResourcesAsync}
                    />
                </View>
            );
        }

        const { navigate } = this.props.navigation;

        return (
            <View style={styles.container}>
                <View style={styles.logoContainer}>
                    <TouchableHighlight
                        style={styles.clickableLogo}
                        underlayColor='#fff'
                        onPress={() =>
                            _onPressImage('http://www.perpetualmotion.org')
                        }
                    >
                        <Image 
                            style={styles.mainLogo}
                            source={require('./img/Perpetualmotionlogo2.png')} 
                        />
                    </TouchableHighlight>
                </View>

                <View style={styles.mainContent}>
                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={[styles.sportButton, {borderBottomColor: '#C3121C'}]}
                                underlayColor='#fff'
                                onPress={() =>
                                    navigate('Sports', {sportID: 1, headerColour: getSportColour(1)})
                                }
                            >
                                <Image 
                                    style={styles.sportLogo}
                                    source={require('./img/ultimate_0.png')}
                                />
                            </TouchableHighlight>
                        </View>

                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={[styles.sportButton, {borderBottomColor: '#0066CC'}]}
                                underlayColor='#fff'
                                onPress={() =>
                                    navigate('Sports', {sportID: 2, headerColour: getSportColour(2)})
                                }
                            >
                                <Image 
                                    style={styles.sportLogo}
                                    source={require('./img/volleyball_0.png')}
                                />
                            </TouchableHighlight>
                        </View>
                    </View>

                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={[styles.sportButton, {borderBottomColor: '#0a790a'}]}
                                underlayColor='#fff'
                                onPress={() =>
                                    navigate('Sports', {sportID: 3, headerColour: getSportColour(3)})
                                }
                            >
                                <Image 
                                    style={styles.sportLogo}
                                    source={require('./img/football_0.png')}
                                />
                            </TouchableHighlight>
                        </View>

                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={[styles.sportButton, {borderBottomColor: '#474F54'}]}
                                underlayColor='#fff'
                                onPress={() =>
                                    navigate('Sports', {sportID: 4, headerColour: getSportColour(4)})
                                }
                            >
                                <Image 
                                    style={styles.sportLogo}
                                    source={require('./img/soccer_0.png')}
                                />
                            </TouchableHighlight>
                        </View>
                    </View>
                </View>

                <View style={styles.footer}>
                    <Text style={styles.mainFooter}>{'\u00A9'} 2002-2018, Perpetual Motion Sports & Entertainment Inc.</Text>
                </View>
            </View>
        );
    }

    _cacheSplashResourcesAsync = async () => {
        const splashImg = require('./img/splash.png');
        // console.warn("splash");
        return Asset.fromModule(splashImg).downloadAsync();
    }

    _cacheResourcesAsync = async () => {
        SplashScreen.hide();
        // console.warn("the rest");
        const images = [
            require('./img/Perpetualmotionlogo2.png'),
            require('./img/ultimate_0.png'),
            require('./img/volleyball_0.png'),
            require('./img/football_0.png'),
            require('./img/soccer_0.png'),
            require('./img/ready-set-play-ultimate.png'),
            require('./img/ready-set-play-volleyball.png'),
            require('./img/ready-set-play-football.png'),
            require('./img/ready-set-play-soccer.png'),
        ];

        const cacheImages = images.map((image) => {
            return Asset.fromModule(image).downloadAsync();
        });

        await Promise.all(cacheImages);
        this.setState({ isAppReady: true });
    }

    _handleLoadingError = (error) => {
        console.warn(error);
    };
}
