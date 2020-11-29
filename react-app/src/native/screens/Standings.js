import React from 'react'
import { Container, Content, Text } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import { StyleSheet } from 'react-native'
import { connect } from 'react-redux'
import Loading from '../components/common/Loading'
import LeagueHelpers from '../../utils/leaguehelpers'
import { fetchLeague } from '../../actions/leagues'

import Colors from '../../../native-base-theme/variables/commonColor';

class Standings extends React.Component {
  state = {
    leagueId: -1
  }

  constructor(props) {
    super(props)
    this.state.leagueId = this.props.route?.params?.leagueId ?? -1
    props.fetchLeague(this.state.leagueId)
  }

  getStandingsInfoFromState = (league) => {
    let info = {}

    if(LeagueHelpers.checkHideSpirit(league) == false){
      info = {
        flexArr: [1, 8, 1, 1, 1, 1, 2],
        tableInfo: {
            header: ['', 'Team', 'W', 'L', 'T', 'P', 'Spirit'],
            data: []
        }
      }

      league.standings.forEach((team, i) => {
          var points = parseInt(team.ties) + (parseInt(team.wins) * 2);
          info.tableInfo.data.push([
            (i + 1), 
            <Text style={styles.link} onPress={() => this.props.navigation.navigate('Team',{league: league, team: team.id, title: team.name})}>{team.name}</Text>, 
            team.wins, 
            team.losses, 
            team.ties, 
            points, 
            parseFloat(team.spiritAverage).toFixed(2)
          ]);
      });
    } else {
      info = {
        flexArr: [1, 8, 1, 1, 1, 1],
        tableInfo: {
            header: ['', 'Team', 'W', 'L', 'T', 'P'],
            data: []
        }
      }

      league.standings.forEach((team, i) => {
          var points = parseInt(team.ties) + (parseInt(team.wins) * 2);
          info.tableInfo.data.push([
            (i+1), 
            <Text style={styles.link} onPress={() => this.props.navigation.navigate('Team',{league: league, team: team.id, title: team.name })}>{team.name}</Text>, 
            team.wins, 
            team.losses, 
            team.ties, 
            points]);
      });
    }

    return info
  }

  render() {
    const { leagueId } = this.state
    const { leagues } = this.props
    const league = leagues[leagueId]
    
    if (league == null || league.isFetching) return <Loading />

    let info = this.getStandingsInfoFromState(league)

    return (
      <Container>
        <Content padder>
          <Table style={styles.table} borderStyle={styles.tableborderstyle}>
            <Row
              flexArr={info.flexArr}
              data={info.tableInfo.header}
              style={styles.header}
              textStyle={styles.headerText}
            />
            {info.tableInfo.data.map((rowData, index) => (
              <Row
                key={index}
                flexArr={info.flexArr}
                data={rowData}
                style={[
                  styles.row,
                  index % 2 == 1 && { backgroundColor: '#e6e6e6' }
                ]}
                textStyle={styles.text}
              />
            ))}
          </Table>
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
  container: { flex: 1, backgroundColor: 'yellow' },
  header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
  headerText: { fontWeight: 'bold' },
  text: {},
  statText: { textAlign: 'center' },
  row: { padding: 8 },
  table: { flex: 1, marginBottom: 10 },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { 
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: 20,
    paddingRight: 20
  },
  link: {color: Colors.brandSecondary}
})
