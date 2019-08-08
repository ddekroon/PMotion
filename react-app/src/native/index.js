import React from 'react'
import {
  StatusBar,
  Platform,
  View,
  Dimensions,
  Image,
  ImageBackground,
  StyleSheet,
  Content
} from 'react-native'
import PropTypes from 'prop-types'
import { Provider } from 'react-redux'
import { PersistGate } from 'redux-persist/es/integration/react'

import { Root, StyleProvider } from 'native-base'
import getTheme from '../../native-base-theme/components'
import theme from '../../native-base-theme/variables/commonColor'

import RootNavigator from './routes/RootNavigator'
import Loading from './components/common/Loading'

// Hide StatusBar on Android as it overlaps tabs
//if (Platform.OS === 'android') StatusBar.setHidden(false);

export default class App extends React.Component {
  static propTypes = {
    store: PropTypes.shape({}).isRequired,
    persistor: PropTypes.shape({}).isRequired
  }

  render() {
    const { store, persistor } = this.props

    let { height, width } = Dimensions.get('window')
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
          <PersistGate loading={<Loading />} persistor={persistor}>
            <StyleProvider style={getTheme(theme)}>
              <RootNavigator />
            </StyleProvider>
          </PersistGate>
        </Provider>
      </Root>
    )
  }
}

/*let styles = StyleSheet.create({
  backgroundImage: {
    position: 'absolute',
    width: 83,
    height: 56
  }
})*/
