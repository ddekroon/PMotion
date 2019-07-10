import React from 'react'
import PropTypes from 'prop-types';
import { Container, Content, Text, View, List, ListItem, Icon, Right, Card, CardItem } from 'native-base';
import Spacer from './Spacer';
import SportHelpers from '../../utils/sporthelpers';
import { Actions } from 'react-native-router-flux';
import DateTimeHelpers from '../../utils/datetimehelpers'
import leaguehelpers from '../../utils/leaguehelpers';

export default class SportLeagues extends React.Component {
    static propTypes = {
        seasons: PropTypes.array.isRequired,
        sportId: PropTypes.string.isRequired,
        sports: PropTypes.array.isRequired
    }

    constructor(props) {
        super(props);
    }

    render() {
        const { sportId, seasons, sports } = this.props;

        const sport = SportHelpers.getSportById(sports, sportId);

        const seasonsView = seasons.map((curSeason) => {
            var leagues = curSeason.leagues.map((league, leagueIndex) =>
                <ListItem key={league.id} onPress={() => Actions.league({ leagueId: league.id, title: leaguehelpers.getFormattedLeagueName(league) })}>
                    <View style={{ flex: 1 }}>
                        <Text key={league.id}>{leaguehelpers.getFormattedLeagueName(league)}</Text>
                    </View>
                    <Right>
                        <Icon name="arrow-forward" />
                    </Right>
                </ListItem>
            )

            var seasonTitle = [];

            if (seasons.length > 1) {
                seasonTitle = [
                    <Text>{curSeason.name} {curSeason.year}</Text>,
                    <Spacer></Spacer>
                ]
            }

            return (
                <Content key={curSeason.name} style={{ flex: 1 }}>
                    {seasonTitle}
                    <List>
                        {leagues}
                    </List>
                </Content>
            );
        });

        return (
            <Container>
                <Content padder>
                    <Card>
                        <CardItem>
                            {seasonsView}
                        </CardItem>
                    </Card>
                </Content>
            </Container>
        );
    }
}

