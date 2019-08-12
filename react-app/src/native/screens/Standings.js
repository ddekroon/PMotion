import React from 'react'
import PropTypes from 'prop-types'
import { Container, Content, Text, Card, CardItem } from 'native-base'
import { Table, Row } from 'react-native-table-component'
import { StyleSheet } from 'react-native'
import { connect } from 'react-redux'

import Loading from '../components/common/Loading'

import LeagueHelpers from '../../utils/leaguehelpers'
import { fetchLeague } from '../../actions/leagues'

class Standings extends React.Component {
  state = {
    leagueId: -1
  }

  constructor(props) {
    super(props)
    this.state.leagueId = this.props.navigation.getParam('leagueId')
    props.fetchLeague(this.state.leagueId)
  }

  checkHideSpirit = () => {
    const { leagues } = this.props;
    const {leagueId} = this.state;
    const league = leagues[leagueId];

    let curDay = new Date().getDay();
    let timeOfDay = new Date().getHours();
    let dayHide = parseInt(league.dayNumber);
    let dayShow = dayHide + parseInt(league.numDaysSpiritHidden);

    if(dayShow > 7){
        dayShow = dayShow % 7;
    }

    if(curDay == dayHide){
        return timeOfDay >= league.hideSpiritHour;
    }

    if(curDay == dayShow){
        return !(timeOfDay >= league.showSpiritHour);
    }

    return (curDay > dayHide && curDay < dayShow || curDay == 1 && dayHide == 7); 
  }


  render() {
    const { leagueId } = this.state
    const { leagues } = this.props
    const league = leagues[leagueId]

    if (league == null || league.isFetching) return <Loading />

    let flexArr = []
    let tableInfo = {}

    if(this.checkHideSpirit() == false){
      flexArr = [1, 8, 1, 1, 1, 1, 2];
      tableInfo = {
          header: ['', 'Team', 'W', 'L', 'T', 'P', 'Spirit'],
          data: [],
      }

      league.standings.forEach((team, i) => {
          var points = parseInt(team.ties) + (parseInt(team.wins) * 2);
          tableInfo.data.push([(i + 1), <Text style={styles.link} onPress={() => this.props.navigation.navigate('Team',{league: league, team: team.id})}>{team.name}</Text>, team.wins, team.losses, team.ties, points, parseFloat(team.spiritAverage).toFixed(2)]);
      });
    }else{

      flexArr = [1, 8, 1, 1, 1, 1];
      tableInfo = {
          header: ['', 'Team', 'W', 'L', 'T', 'P'],
          data: [],
      }

      league.standings.forEach((team, i) => {
          var points = parseInt(team.ties) + (parseInt(team.wins) * 2);
          tableInfo.data.push([(i+1), <Text style={styles.link} onPress={() => this.props.navigation.navigate('Team',{league: league, team: team.id})}>{team.name}</Text>, team.wins, team.losses, team.ties, points]);
      });

    }

    return (
      <Container>
        <Content padder>
          <Card>
            <CardItem header>
              <Text>{LeagueHelpers.getFormattedLeagueName(league)}</Text>
            </CardItem>
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
                      index % 2 == 1 && { backgroundColor: '#e6e6e6' }
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
  container: { flex: 1, backgroundColor: 'yellow' },
  header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
  headerText: { fontWeight: 'bold' },
  text: {},
  statText: { textAlign: 'center' },
  row: { padding: 2 },
  table: { flex: 1, marginBottom: 10 },
  tableborderstyle: { borderWidth: 0, borderColor: 'transparent' },
  cardItem: { padding: 10 },
  link: {color: 'red'}
})
