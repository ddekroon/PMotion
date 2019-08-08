import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { createAppContainer, createStackNavigator } from 'react-navigation'

import MainNavigator from './MainNavigator'
import LeaguePage from '../components/leagues/League'
import Loading from '../components/common/Loading'

import NavigationProps from '../constants/navigation'

import { getLookups } from '../../actions/lookups'

const RootStack = createStackNavigator(
  {
    Main: MainNavigator,
    League: LeaguePage
  },
  {
    initialRouteName: 'League',
    mode: 'modal',
    defaultNavigationOptions: NavigationProps.navbarProps
  }
)

const RootContainer = createAppContainer(RootStack)

class RootNavigator extends React.Component {
  static propTypes = {
    isFetching: PropTypes.bool.isRequired,
    fetchLookups: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    props.fetchLookups()
  }

  render() {
    const { isFetching } = this.props

    if (isFetching) return <Loading />

    return <RootContainer />
  }
}

const mapStateToProps = state => ({
  isFetching: state.lookups.isFetching
})

const mapDispatchToProps = {
  fetchLookups: getLookups
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(RootNavigator)
