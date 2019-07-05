import React from 'react';
import { StatusBar, Platform, View, Dimensions, Image, ImageBackground, StyleSheet, Content } from 'react-native';
import { Font } from 'expo';
import PropTypes from 'prop-types';
import { Provider } from 'react-redux';
import { Router, Stack } from 'react-native-router-flux';
import { PersistGate } from 'redux-persist/es/integration/react';

import { Root, StyleProvider } from 'native-base';
import getTheme from '../../native-base-theme/components';
import theme from '../../native-base-theme/variables/commonColor';

import Routes from './routes/index';
import Loading from './components/Loading';

import { getLookups } from '../actions/lookups';

// Hide StatusBar on Android as it overlaps tabs
//if (Platform.OS === 'android') StatusBar.setHidden(false);

export default class App extends React.Component {
  static propTypes = {
    store: PropTypes.shape({}).isRequired,
    persistor: PropTypes.shape({}).isRequired,
  }

  async componentWillMount() {
    await Font.loadAsync({
      Roboto: require('native-base/Fonts/Roboto.ttf'),
      Roboto_medium: require('native-base/Fonts/Roboto_medium.ttf'),
      Ionicons: require('@expo/vector-icons/fonts/Ionicons.ttf'),
    });

    await getLookups();
  }

  render() {
    const { store, persistor } = this.props;

    let { height, width } = Dimensions.get('window');
    //console.log(height + " " + width);


    return (
      <Root>
        <StatusBar barStyle="default" hidden={false} translucent={true} />
        {/*<View
          style={{backgroundColor:'blue',width:width,height:height}}
        >
          <ImageBackground
            source={require('../images/logo-grey.png')}
          style={{width:'100%',height:'100%'}}>*/}

        <Provider store={store}>
          <PersistGate
            loading={<Loading />}
            persistor={persistor}
          >
            <StyleProvider style={getTheme(theme)}>
              <Router>
                <Stack key="root">
                  {Routes}
                </Stack>
              </Router>
            </StyleProvider>
          </PersistGate>
        </Provider>
      </Root>
    );
  }
}

let styles = StyleSheet.create({
  backgroundImage: {
    position: 'absolute',
    width: 83,
    height: 56
  }
});
