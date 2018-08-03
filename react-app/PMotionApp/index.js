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
import App from './App.js';
import styles from './styles.js';

export default class Index extends Component {

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
                        style={styles.mainLogo}
                        source={require('./img/Perpetualmotionlogo2.png')} 
                    />
                </View>

                <View style={styles.mainContent}>
                    <View style={styles.contentRow}>
                        <View style={styles.buttonContainer}>
                            <TouchableHighlight
                                style={[styles.sportButton, {borderBottomColor: '#C3121C'}]}
                                onPress={() =>
                                    navigate('Sports', {sportID: 1})
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
                                onPress={() =>
                                    navigate('Sports', {sportID: 2})
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
                                onPress={() =>
                                    navigate('Sports', {sportID: 3})
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
                                onPress={() =>
                                    navigate('Sports', {sportID: 4})
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
}

const titleConfig = {
            title: 'Perpetual Motion Mobile',
            style: {color: '#fff'}
};
