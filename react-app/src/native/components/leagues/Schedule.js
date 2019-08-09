import React from 'react'
import { Container, Content } from 'native-base'
import { connect } from 'react-redux'
import PropTypes from 'prop-types'

import Loading from '../common/Loading'
import TeamList from './TeamList'
import ScheduleWeek from './ScheduleWeek'
import ByeWeek from './ByeWeek'

import { fetchLeague } from '../../../actions/leagues'

class Schedule extends React.Component {
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
        <Content>
          <TeamList league={league} />

          {league.leagueSchedule.map((week, i) => {
            if (Object.keys(week.times).length === 0) {
              return <ByeWeek key={week.date.id} scheduleWeek={week} />
            } else {
              return (
                <ScheduleWeek
                  key={week.date.id}
                  league={league}
                  scheduleWeek={week}
                />
              )
            }
          })}
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
)(Schedule)
