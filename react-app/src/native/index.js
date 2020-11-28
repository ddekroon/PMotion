import React from 'react'
import { StatusBar } from 'react-native'
import PropTypes from 'prop-types'
import { Provider } from 'react-redux'
import { PersistGate } from 'redux-persist/es/integration/react'

import { Root, StyleProvider } from 'native-base'
import getTheme from '../../native-base-theme/components'
import theme from '../../native-base-theme/variables/commonColor'

import RootNavigator from './navigators/RootNavigator'
import Loading from './components/common/Loading'

import Colors from '../../native-base-theme/variables/commonColor';

export default class App extends React.Component {
  static propTypes = {
    store: PropTypes.shape({}).isRequired,
    persistor: PropTypes.shape({}).isRequired
  }

  render() {
    const { store, persistor } = this.props

    return (
      <Root>
        <StatusBar barStyle="light-content" backgroundColor={Colors.brandPrimary} />
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
