import React from 'react'
import { Text, Card, CardItem } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import { StyleSheet } from 'react-native'
import PropTypes from 'prop-types'

import Loading from '../common/Loading'

import CommonColors from '../../../../native-base-theme/variables/commonColor'

export default class TeamList extends React.Component {
  static propTypes = {
    league: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { league } = this.props

    const flexArr = [1, 10]
    const teamTable = {
      header: ['', 'Name'],
      data: []
    }

    league.teams
      .sort(
        (teamOne, teamTwo) =>
          parseInt(teamTwo.getNumInLeague) - parseInt(teamOne.getNumInLeague)
      )
      .map((team, i) => {
        teamTable.data.push([team.numInLeague, team.name])
      })

    if (league == null || league.isFetching) return <Loading />

    return (
      <Card>
        <CardItem cardBody style={styles.cardItem}>
          <Table style={styles.table} borderStyle={styles.tableborderstyle}>
            <Row
              flexArr={flexArr}
              data={teamTable.header}
              style={styles.header}
              textStyle={styles.headerText}
            />
            {teamTable.data.map((rowData, index) => (
              <Row
                key={index}
                flexArr={flexArr}
                data={rowData}
                style={[
                  styles.row,
                  index % 2 == 1 && {
                    backgroundColor: CommonColors.brandLightGray
                  }
                ]}
                textStyle={styles.text}
              />
            ))}
          </Table>
        </CardItem>
      </Card>
    )
  }
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  header: {
    padding: 2,
    borderBottomWidth: 2,
    borderBottomColor: CommonColors.brandDarkGray
  },
  headerText: { fontWeight: 'bold' },
  text: {},
  row: { padding: 2 },
  table: {
    flex: 1,
    marginBottom: CommonColors.contentPadding,
    marginTop: CommonColors.contentPadding
  },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { padding: 10 }
})
