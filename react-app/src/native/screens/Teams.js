import React from 'react'
import { Container, Content } from 'native-base'
import { connect } from 'react-redux'

import Loading from '../components/common/Loading'
import TeamList from '../components/leagues/TeamList'

import { fetchLeague } from '../../actions/leagues'

class Teams extends React.Component {
  state = {
    leagueId: -1
  }

  constructor(props) {
    super(props)
    this.state.leagueId = this.props.navigation.getParam('leagueId')
    props.fetchLeague(this.state.leagueId)
  }

  render() {
    const { leagueId } = this.state
    const { leagues } = this.props
    const league = leagues[leagueId]

    if (league == null || league.isFetching) return <Loading />

    return (
      <Container>
        <Content padder>
          <TeamList league={league} />
        </Content>
      </Container>
    )
  }
}

const mapStateToProps = state => ({
  leagues: state.leagues || {}
})

//map actions to components
const mapDispatchToProps = {
  fetchLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Teams)
