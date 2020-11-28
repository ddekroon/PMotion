import React from 'react'
import { Text, Card, CardItem, Grid, Col, View } from 'native-base'
import { StyleSheet } from 'react-native'
import Loading from '../common/Loading'
import PropTypes from 'prop-types'

import Colors from '../../../../native-base-theme/variables/commonColor';

export default class TeamList extends React.Component {
  static propTypes = {
    league: PropTypes.object.isRequired,
    title: PropTypes.string
  }

  constructor(props) {
    super(props)
  }

  renderTeamsCol = (league, teams) => {
    const { navigation } = this.props

    return (
      <Col>
        {teams.map((team, index) =>
          <View key={index} style={{marginBottom: 7}}>
            <Text onPress={() => navigation.navigate('Team', {league: league, team: team.id})}>
              {team.numInLeague}. 
              <Text style={styles.link}> {team.name}</Text>
            </Text>
          </View>
        )}
      </Col>
    )
  }

  render() {
    const { league, title } = this.props

    if (league == null || league.isFetching) return <Loading />

    let halfTeams = Math.ceil(league.teams.length / 2);  

    return (
      <Card>
        {title && (
          <CardItem cardHeader><Text>{title}</Text></CardItem>
        )}
        <CardItem cardBody style={styles.cardItem}>
          <Grid>
            {this.renderTeamsCol(league, league.teams.slice(0, halfTeams))}
            {this.renderTeamsCol(league, league.teams.slice(halfTeams, league.teams.length))}
          </Grid>
        </CardItem>
      </Card>
    )
  }
}

const styles = StyleSheet.create({
  cardItem: { 
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: 20,
    paddingRight: 20
  },
  link: {color: Colors.brandSecondary}
})
