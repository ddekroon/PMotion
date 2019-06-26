import React from 'react'
import { H1, Container, Content, Text } from 'native-base'

export default class Ultimate extends React.Component {
    render() {
        const seasons = Object.values(this.props.seasonsWithLeaguesBySport[this.props.currentSport]);

        const seasonsView = seasons.map((curSeason) => {
            var leagues = curSeason.leagues.map((league, leagueIndex) =>
                <Text key={league.id}>{league.name}</Text>
            )
            return <Content key={curSeason.name}><Text>{curSeason.name} {curSeason.year}</Text>{leagues}</Content>;
        });

        return (
            <Container>
                <Content padder>
                    <H1>Ultimate Frisbee</H1>
                    {seasonsView}
                </Content>
            </Container>
        );
    }
}
