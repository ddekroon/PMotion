import React from 'react'
import { StyleSheet, Image } from 'react-native'
import PropTypes from 'prop-types'

import { Text, View, Grid, Col, List, ListItem } from 'native-base'
import Loading from '../common/Loading'

import LeagueHelpers from '../../../utils/leaguehelpers'
import { TouchableOpacity } from 'react-native-gesture-handler';

import Colors from '../../../../native-base-theme/variables/commonColor';
import Images from '../../../images/index'

export default class ScheduleWeek extends React.Component {
  static propTypes = {
    scheduleWeek: PropTypes.object.isRequired,
    league: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  renderTime = (time) => {

    return <View key={time} style={styles.timeList}> 
      <List>
        {time.matches.map((match, index) => (
          <ListItem key={index} style={styles.match}>
            {match.playoff1 === '' && match.playoff2 === ''
              ? this.renderMatch(time.time, match)
              : this.renderPlayoffMatch(time.time, match)
            }
          </ListItem>
        ))}
      </List>
    </View>
  }

  renderMatch = (time, match) => {
    const { league, navigation } = this.props

    let disableClick = match.venue === 'Practice Area';

    return (
      <Grid>
        <Col>
          <View>
            <Text style={{...styles.teamName, marginBottom: 8 }} onPress={() => navigation.push('Team', {team: match.team1, league: league, title: LeagueHelpers.getTeamName(league, match.team1)})}>
              <Image style={{ width: 20, height: 20, backgroundColor: 'transparent' }} source={Images.icons.shirtDark} />
              {" " + LeagueHelpers.getTeamName(league, match.team1)}
            </Text>
          </View>
          <View>
            <Text style={styles.teamName} onPress={() => navigation.push('Team', {team: match.team2, league: league, title: LeagueHelpers.getTeamName(league, match.team2) })}>
              <Image style={{ width: 20, height: 20, backgroundColor: 'transparent' }} source={Images.icons.shirtWhite} />
              {" " + LeagueHelpers.getTeamName(league, match.team2)}
            </Text>
          </View>
        </Col>
        <Col>
          <View>
            <Text style={{...styles.time, height: 25, marginTop: 3, marginBottom: 8 }}>{LeagueHelpers.convertMatchTime(time)}</Text>
          </View>
          <View>
            <TouchableOpacity disabled={disableClick} onPress={() => this.props.navigation.push('Maps', {venue: match.venue})}>
              <Text style={styles.venue}>{match.venue}</Text>
            </TouchableOpacity>
          </View>
        </Col>
      </Grid>
    )
  }

  renderPlayoffMatch = (time, match) => {
    return (
      <Grid>
        <Col>
          <View>
            <Text style={{marginBottom: 8}}>{match.playoff1}</Text>
          </View>
          <View>
            <Text>{match.playoff2}</Text>
          </View>
        </Col>
        <Col>
          <View>
            <Text style={{...styles.time, marginBottom: 8}}>{LeagueHelpers.convertMatchTime(time)}</Text>
          </View>
          <View>
            <Text style={styles.venue}>{match.venue}</Text>
          </View>
        </Col>
      </Grid>
    )
  }

  render() {
    const { scheduleWeek, league } = this.props

    if (league == null || league.isFetching) return <Loading />

    return (
      <View>
        <Grid>
          <Col><Text style={styles.headerText}>Week {scheduleWeek.date.weekNumber}</Text></Col>
          <Col><Text style={{...styles.headerText, textAlign: 'right'}}>{scheduleWeek.date.description}</Text></Col>
        </Grid>
        
        {
          Object.keys(scheduleWeek.times).length > 0
            ? Object.keys(scheduleWeek.times).map(x => this.renderTime(scheduleWeek.times[x]))
            : (
              <View style={styles.timeList}><List><ListItem key={0} style={{ marginLeft: 0 }}>
                <Grid style={{ alignItems: 'center' }}><Col><Text style={{ textAlign: 'right' }}>Bye Week</Text></Col></Grid>
              </ListItem></List></View>
            )
        }
      </View>
    )
  }
}

const styles = StyleSheet.create({
  headerText: { fontWeight: 'bold' },
  timeList: { marginBottom: 20 },
  match: { paddingTop: 5, paddingBottom: 5, paddingRight: 0, paddingLeft: 0, marginLeft: 0 },
  teamName: { color: Colors.brandSecondary },
  title: { textAlign:'center', fontWeight: 'bold' },
  time: { textAlign: 'right' },
  venue: { textAlign: 'right', color: Colors.brandSecondary }
})
