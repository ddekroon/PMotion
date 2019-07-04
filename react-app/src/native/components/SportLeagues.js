import React from 'react'
import PropTypes from 'prop-types';
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right } from 'native-base';
import Spacer from './Spacer';
import Loading from './Loading';
import SportHelpers from '../../utils/sporthelpers';
import { Actions } from 'react-native-router-flux';

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
                <ListItem key={league.id} onPress={() => Actions.leaguePage({leagueId: league.id, leagueName: league.name})}>
                    <Body>
                        <Text key={league.id}>{league.name}</Text>
                    </Body>
                    <Right>
                        <Icon name="arrow-forward" />
                    </Right>
                </ListItem>

            )
            return (
                <Content key={curSeason.name}>
                    <Text>{curSeason.name} {curSeason.year}</Text>
                    <Spacer />
                    <List>
                        {leagues}
                    </List>
                </Content>
            );
        });

        return (
            <Container>
                <Content padder>
                    <H1>{sport.name}</H1>
                    <Spacer />
                    {seasonsView}
                </Content>
            </Container>
        );
    }
}

