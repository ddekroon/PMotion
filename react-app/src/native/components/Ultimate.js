import React from 'react'
import { H1, Container, Content, Text } from 'native-base'

export default class Ultimate extends React.Component {
    render() {
        console.log('this.props.screenProps.seasons in Ultimate', this.props.screenProps.data.seasons);
        /* No idea why this isn't working.. getting an error on android saying basically seasons is undefined. Pretty junk.
        const seasonsView = this.props.screenProps.data.seasons.map((curSeason) => {
            var leagues = curSeason.leagues.map((league, leagueIndex) =>
                <Text key={league.id}>{league.name}</Text>
            )
            return <Content><Text key={curSeason.name}>{curSeason.name} {curSeason.year}</Text>{leagues}</Content>;
        });
        console.log(seasonsView);*/

        return (
            <Container>
                <Content padder>
                    <H1>Ultimate Frisbee</H1>
                    <Text>{this.props.screenProps.data.label}</Text>
                    {<Text>{JSON.stringify(this.props.screenProps.data.seasons, null, 2)}</Text>}
                    {/*seasonsView*/}
                </Content>
            </Container>
        );
    }
}
