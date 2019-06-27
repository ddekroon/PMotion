import React from 'react'
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right} from 'native-base';
import Spacer from '../Spacer';

export default class Ultimate extends React.Component {
 
    render() {

        const seasons = Object.values(this.props.seasonsWithLeaguesBySport[this.props.currentSport]);

        const seasonsView = seasons.map((curSeason) => {
            var leagues = curSeason.leagues.map((league, leagueIndex) =>
                <ListItem key={league.id} onPress={()=> this.props.navigation.navigate(league.name.replace(/\s+|\//g,''))}>
                    <Body>
                        <Text key={league.id}>{league.name}</Text>
                    </Body>
                    <Right>
                        <Icon name="arrow-forward"/>
                    </Right>
                </ListItem>

            )
            return (
                <Content key={curSeason.name}>
                    <Text>{curSeason.name} {curSeason.year}</Text>
                    <Spacer/>
                    <List>
                        {leagues}
                    </List>
                </Content>
            );
        });

        return (
            <Container>
                <Content padder>
                    <H1>Ultimate Frisbee</H1>
                    <Spacer/>
                    {seasonsView}
                </Content>
            </Container>
        );
    }
}


/*
<Left>
<Icon name="person-add" />
</Left>
*/