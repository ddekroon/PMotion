import React from 'react'
import { Container, Content, Text, Card, CardItem } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import { StyleSheet } from 'react-native'
import { connect } from 'react-redux'

import Loading from '../components/common/Loading'

import CommonColors from '../../../native-base-theme/variables/commonColor'
import { fetchLeague } from '../../actions/leagues'
import LeagueHelpers from '../../utils/leaguehelpers'

class Standings extends React.Component {
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

    const flexArr = [1, 8, 1, 1, 1, 1, 2]
    const tableInfo = {
      header: [
        '',
        <Text style={[styles.headerText, styles.textLeft, styles.cellText]}>
          Team
        </Text>,
        'W',
        'L',
        'T',
        'P'
      ],
      data: []
    }

    if (!LeagueHelpers.checkHideSpirit(league)) {
      tableInfo.header.push('Spirit')
    }

    league.standings.forEach((team, i) => {
      var points = parseInt(team.ties) + parseInt(team.wins) * 2
      var teamRow = [
        i + 1,
        <Text style={[styles.textLeft, styles.cellText]}>{team.name}</Text>,
        team.wins,
        team.losses,
        team.ties,
        points
      ]

      if (!LeagueHelpers.checkHideSpirit(league)) {
        teamRow.push(parseFloat(team.spiritAverage).toFixed(2))
      }

      tableInfo.data.push(teamRow)
    })

    return (
      <Container>
        <Content padder>
          <Card>
            <CardItem cardBody style={styles.cardItem}>
              <Table style={styles.table} borderStyle={styles.tableborderstyle}>
                <Row
                  flexArr={flexArr}
                  data={tableInfo.header}
                  style={styles.header}
                  textStyle={styles.headerText}
                />
                {tableInfo.data.map((rowData, index) => (
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
)(Standings)

const styles = StyleSheet.create({
  header: {
    padding: 2,
    borderBottomWidth: 2,
    borderBottomColor: CommonColors.brandDarkGray
  },
  headerText: { fontWeight: 'bold', textAlign: 'center' },
  text: { textAlign: 'center' },
  row: { padding: 2 },
  table: {
    flex: 1,
    marginBottom: CommonColors.contentPadding,
    marginTop: CommonColors.contentPadding
  },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { padding: 10 },
  cellText: {
    marginTop: -1
  },
  textLeft: { textAlign: 'left' }
})
