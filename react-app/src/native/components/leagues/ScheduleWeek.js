import React from 'react'
import { StyleSheet } from 'react-native'
import PropTypes from 'prop-types'

import { Text, Card, CardItem } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import Loading from '../common/Loading'

import LeagueHelpers from '../../../utils/leaguehelpers'
import commonColor from '../../../../native-base-theme/variables/commonColor'

export default class ScheduleWeek extends React.Component {
  static propTypes = {
    scheduleWeek: PropTypes.object.isRequired,
    league: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { scheduleWeek, league } = this.props

    const flexArr = [5, 5, 1, 5]
    const timeTemplate = {
      header: ['', 'Dark', '', 'White'],
      data: []
    }

    const timeTables = Object.keys(scheduleWeek.times).map(
      (time, timeIndex) => {
        var timeTable = JSON.parse(JSON.stringify(timeTemplate))
        timeTable.header[0] = (
          <Text style={styles.time} key={timeIndex}>
            {LeagueHelpers.convertMatchTime(scheduleWeek.times[time].time)}
          </Text>
        )

        timeTable.data = scheduleWeek.times[time].matches.map(match => {
          if (match.playoff1 === '' && match.playoff2 === '') {
            return [
              <Text style={styles.venue}>{match.venue}</Text>,
              LeagueHelpers.getTeamName(league, match.team1),
              'vs',
              LeagueHelpers.getTeamName(league, match.team2)
            ]
          } else {
            return [
              <Text style={styles.venue}>{match.venue}</Text>,
              match.playoff1,
              'vs',
              match.playoff2
            ]
          }
        })

        return timeTable
      }
    )

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
            {timeTables.map((timeTable, timeTableIndex) => {
              var objectArray = []

              objectArray.push(
                <Row
                  key={timeTableIndex}
                  flexArr={flexArr}
                  data={timeTable.header}
                  style={styles.header}
                  textStyle={styles.headerText}
                />
              )

              objectArray.push(
                timeTable.data.map((rowData, matchIndex) => (
                  <Row
                    key={timeTableIndex + '-' + matchIndex}
                    flexArr={flexArr}
                    data={rowData}
                    style={[
                      styles.row,
                      matchIndex % 2 == 1 && {
                        backgroundColor: commonColor.brandLightGray
                      }
                    ]}
                    textStyle={styles.text}
                  />
                ))
              )

              return objectArray
            })}
          </Table>
        </CardItem>
      </Card>
    )
  }
}

const styles = StyleSheet.create({
  header: {
    padding: 2,
    alignItems: 'baseline',
    borderBottomWidth: 2,
    borderBottomColor: commonColor.brandDarkGray
  },
  headerText: { fontWeight: 'bold', textAlign: 'center' },
  time: {
    fontWeight: 'bold',
    fontSize: commonColor.fontSizeBase * 1.1,
    textAlign: 'left'
  },
  row: { padding: 2, alignItems: 'center' },
  text: {
    fontSize: commonColor.fontSizeBase * 0.8,
    textAlign: 'center'
  },
  venue: {
    fontSize: commonColor.fontSizeBase * 0.8,
    textAlign: 'left',
    marginTop: -1,
    borderRightWidth: 1,
    borderRightColor: commonColor.brandDarkGray
  },
  table: { flex: 1, marginBottom: 10 },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { flex: 1, padding: commonColor.contentPadding }
})
