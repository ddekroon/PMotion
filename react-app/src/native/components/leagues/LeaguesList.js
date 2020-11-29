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
  H3
} from 'native-base'

import LeagueHelpers from '../../../utils/leaguehelpers'

export default class LeaguesList extends React.Component {
  static propTypes = {
    seasons: PropTypes.array.isRequired,
    navigation: PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  renderSeasons = (seasons) => {
    return (
      <View>
        {seasons.map((x) => (
           <View key={x.name}>
            {seasons.length > 1 && (<H3>{x.name} {x.year}</H3>)}
            {this.renderLeagues(x.leagues)}
          </View>
        ))}
      </View>
    )
  }

  renderLeagues = (leagues) => {
    const { navigation } = this.props

    return leagues.length > 0 ? (
      <List>
        {leagues.map((league) => (
          <ListItem
            key={league.id}
            onPress={() => {
              navigation.navigation.push('League', {
                leagueId: league.id,
                title: LeagueHelpers.getFormattedLeagueName(league),
                addTeamList: false,
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
        ))}
      </List>
    ) : (
      <View style={{padding: 20}}>
        <Text style={{textAlign:'center'}}>No leagues for current season</Text>
      </View>
    )
  }

  render() {
    const { seasons } = this.props

    return (
      <Container>
        <Content>
          {this.renderSeasons(seasons)}
        </Content>
      </Container>
    )
  }
}
