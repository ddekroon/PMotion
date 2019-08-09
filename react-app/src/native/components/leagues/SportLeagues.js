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

import { connect } from 'react-redux'

import Spacer from '../common/Spacer'
import SportHelpers from '../../../utils/sporthelpers'
import LeagueHelpers from '../../../utils/leaguehelpers'

class SportLeagues extends React.Component {
  static propTypes = {}

  static navigationOptions = {}

  constructor(props) {
    super(props)
  }

  render() {
    const { sports, scoreReporterSeasons } = this.props.lookups
    const { sportId, navigation } = this.props.screenProps

    const sport = SportHelpers.getSportById(sports, sportId)
    const seasons = scoreReporterSeasons[sportId]

    const seasonsView = scoreReporterSeasons[sportId].map(curSeason => {
      var leagues = curSeason.leagues.map((league, leagueIndex) => (
        <ListItem
          key={league.id}
          onPress={() => {
            navigation.push('League', {
              leagueId: league.id,
              title: LeagueHelpers.getFormattedLeagueName(league)
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

const mapStateToProps = state => ({
  lookups: state.lookups || {}
})

//map actions to components
const mapDispatchToProps = {}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(SportLeagues)
