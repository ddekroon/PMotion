import React from 'react'
import PropTypes from 'prop-types'

import {
  Container,
  Content,
  Text,
  View,
  List,
  ListItem,
  Icon,
  Right,
  Card,
  CardItem
} from 'native-base'

import Spacer from '../common/Spacer'

import LeagueHelpers from '../../../utils/leaguehelpers'

export default class LeaguesList extends React.Component {
  static propTypes = {
    seasons: PropTypes.array.isRequired,
    navigation: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { seasons, navigation } = this.props

    const seasonsView = seasons.map(curSeason => {
      var leagues = curSeason.leagues.map((league, leagueIndex) => (
        <ListItem
          key={league.id}
          onPress={() => {
            navigation.navigation.push('League', {
              leagueId: league.id,
              title: LeagueHelpers.getFormattedLeagueName(league),
              addTeamList: true,
            })
          }}
        >
          <View style={{ flex: 1 }}>
            <Text key={league.id}>
              {LeagueHelpers.getFormattedLeagueName(league)}
            </Text>
          </View>
          <Right>
            <Icon name="arrow-forward" />
          </Right>
        </ListItem>
      ))

      var seasonTitle = []

      if (seasons.length > 1) {
        seasonTitle = [
          <Text>
            {curSeason.name} {curSeason.year}
          </Text>,
          <Spacer />
        ]
      }

      return (
        <Content key={curSeason.name} style={{ flex: 1 }}>
          {seasonTitle}
          <List>{leagues}</List>
        </Content>
      )
    })

    return (
      <Container>
        <Content padder>
          <Card>
            <CardItem>{seasonsView}</CardItem>
          </Card>
        </Content>
      </Container>
    )
  }
}
