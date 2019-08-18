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

import RootNavigator from './navigators/RootNavigator'
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

    return (
      <Root>
        <StatusBar
          barStyle="default"
          hidden={false}
          translucent={true}
          barStyle="light-content"
        />
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
