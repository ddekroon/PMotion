import React from 'react'
import { Container, Content, View, Text } from 'native-base'
import { connect } from 'react-redux'

import Loading from '../components/common/Loading'
import TeamList from '../components/leagues/TeamList'
import ScheduleWeek from '../components/leagues/ScheduleWeek'

import { fetchLeague } from '../../actions/leagues'

class Schedule extends React.Component {
  state = {
    leagueId: -1
  }

  constructor(props) {
    super(props)
    this.state.leagueId = this.props.route?.params?.leagueId ?? -1
    //props.fetchLeague(this.state.leagueId) league will be fetched by standings tab
  }

  render() {
    const { leagueId } = this.state
    const { leagues, route, navigation } = this.props
    const league = leagues[leagueId]
    const addTeamList = route.params?.addTeamList ?? false;

    if (league == null || league.isFetching) return <Loading />

    return (
      <Container>
        <Content padder>

          {addTeamList && (<TeamList league={league} title="Teams" navigation={navigation} />)}

          {league.leagueSchedule.map((week, i) => (
              <ScheduleWeek
                key={week.date.id}
                league={league}
                scheduleWeek={week}
                navigation={navigation}
              />
            ))
          }
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
