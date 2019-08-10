import React from 'react'
import { connect } from 'react-redux'

import LeaguesList from '../components/leagues/LeaguesList'

import SportHelpers from '../../utils/sporthelpers'

class SportLeagues extends React.Component {
  static navigationOptions = {}

  constructor(props) {
    super(props)
  }

  render() {
    const { sports, scoreReporterSeasons } = this.props.lookups
    const { sportId, navigation } = this.props.screenProps

    const sport = SportHelpers.getSportById(sports, sportId)
    const seasons = scoreReporterSeasons[sportId]

    return <LeaguesList seasons={seasons} navigation={navigation} />
  }
}

const mapStateToProps = state => ({
  lookups: state.lookups || {}
})

//map actions to components
const mapDispatchToProps = {}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(SportLeagues)
