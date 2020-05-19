import React from 'react'
import { StyleSheet } from 'react-native'
import PropTypes from 'prop-types'

import { Text, Card, CardItem } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import Loading from '../common/Loading'

import LeagueHelpers from '../../../utils/leaguehelpers'
import { TouchableOpacity } from 'react-native-gesture-handler';

export default class ScheduleWeek extends React.Component {
  static propTypes = {
    scheduleWeek: PropTypes.object.isRequired,
    league: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { scheduleWeek, league, navigation } = this.props

    const flexArr = [6, 5, 2, 5]
    const weekTable = {
      header: [
        <Text style={styles.headerText}>Field</Text>,
        <Text style={styles.title}>Dark</Text>,
        '',
        <Text style={styles.title}>White</Text>
      ],
      data: []
    }

    Object.keys(scheduleWeek.times).forEach(time => {
      weekTable.data.push([
        LeagueHelpers.convertMatchTime(scheduleWeek.times[time].time),
        '',
        '',
        ''
      ])
      scheduleWeek.times[time].matches.forEach(match => {
        let disableClick = false;
        if(match.venue === 'Practice Area'){
          disableClick = true;
        }

        if (match.playoff1 === '' && match.playoff2 === '') {
          weekTable.data.push([
            <TouchableOpacity disabled={disableClick} onPress={() => this.props.navigation.push('Maps', {venue: match.venue})}>
              <Text style={styles.venue}>
              {match.venue}
              </Text>
            </TouchableOpacity>,
            <Text style={styles.teamName} onPress={() => navigation.push('Team', {team: match.team1, league: league})}>
              {LeagueHelpers.getTeamName(league, match.team1)}
            </Text>,
            <Text style={styles.center}>vs</Text>,
            <Text style={styles.teamName} onPress={() => navigation.push('Team', {team: match.team2, league: league})}>
              {LeagueHelpers.getTeamName(league, match.team2)}
            </Text>
          ])
        } else {
          weekTable.data.push([
            match.venue,
            <Text style={styles.center}>{match.playoff1}</Text>,
            <Text style={styles.center}>vs</Text>,
            <Text style={styles.center}>{match.playoff2}</Text>
          ])
        }
      })
    })

    if (league == null || league.isFetching) return <Loading />

    return (
      <Card>
        <CardItem header>
          <Text>
            {scheduleWeek.date.description} - Week{' '}
            {scheduleWeek.date.weekNumber}
          </Text>
        </CardItem>
        <CardItem cardBody style={styles.cardItem}>
          <Table style={styles.table} borderStyle={styles.tableborderstyle}>
            <Row
              flexArr={flexArr}
              data={weekTable.header}
              style={styles.header}
              textStyle={styles.headerText}
            />
            {weekTable.data.map((rowData, index) => (
              <Row
                key={index}
                flexArr={flexArr}
                data={rowData}
                style={[
                  styles.row,
                  index % 2 == 1 && { backgroundColor: '#e6e6e6' }
                ]}
                textStyle={[
                  styles.text,
                  rowData[2] === '' && { fontWeight: 'bold', fontSize: 20 }
                ]}
              />
            ))}
          </Table>
        </CardItem>
      </Card>
    )
  }
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: 'yellow' },
  header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
  headerText: { fontWeight: 'bold' },
  text: {},
  row: { padding: 2 },
  table: { flex: 1, marginBottom: 10 },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { padding: 10 },
  teamName: { textAlign: 'center', color: 'red' },
  center: {textAlign: 'center'},
  title: {textAlign:'center', fontWeight: 'bold'},
  venue: {textDecorationLine: 'underline'}

})
