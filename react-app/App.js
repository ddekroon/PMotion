import React, { Component } from 'react'
import Root from './src/native/index'
import configureStore from './src/store/index'
import * as Font from 'expo-font'
import Loading from './src/native/components/common/Loading'

const { persistor, store } = configureStore()

export default class App extends Component {
  state = {
    loading: true
  }

  constructor(props) {
    super(props)
  }

  async componentDidMount() {
    await Font.loadAsync({
      Roboto: require('native-base/Fonts/Roboto.ttf'),
      Roboto_medium: require('native-base/Fonts/Roboto_medium.ttf')
    })
    this.setState({ loading: false })
  }

  render() {
    if (this.state.loading) return <Loading />

    return <Root store={store} persistor={persistor} />
  }
}
