import React from 'react';
import { Image, Text, View } from 'react-native';
import { Asset, AppLoading, SplashScreen } from 'expo';

export default class App extends React.Component {
  state = {
    isSplashReady: false,
    isAppReady: false,
  };

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
      return (
        <View style={{ flex: 1 }}>
          <Image
            source={require('./img/splash.png')}
            onLoad={this._cacheResourcesAsync}
          />
        </View>
      );
    }

    return (
      <View style={{ flex: 1 }}>
        <Image source={require('./img/soccer_0.png')} />
        <Image source={require('./img/football_0.png')} />
      </View>
    );    
  }

  _cacheSplashResourcesAsync = async () => {
    const gif = require('./img/splash.png');
    return Asset.fromModule(gif).downloadAsync()
  }

  _cacheResourcesAsync = async () => {
    SplashScreen.hide();
    const images = [
      require('./img/soccer_0.png'),
      require('./img/football_0.png'),
    ];

    const cacheImages = images.map((image) => {
      return Asset.fromModule(image).downloadAsync();
    });

    await Promise.all(cacheImages);
    this.setState({ isAppReady: true });
  }
}
