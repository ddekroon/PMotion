import React from 'react'
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right } from 'native-base';
import Spacer from './Spacer';
import Loading from './Loading';

export default class sportLeagueNav extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            sport: {},
            loading: true
        };
    }

    componentDidMount() {
        let component = this;
        let curSport = this.props.sports.find(curSport => {
            return curSport.id == component.props.sportId
        });

        this.setState({
            sport: curSport,
            loading: false
        });
    }

    render() {
        if (this.state.loading) return <Loading />;

        const seasons = Object.values(this.props.seasonsWithLeaguesBySport[this.state.sport.id]);

        const seasonsView = seasons.map((curSeason) => {
            var leagues = curSeason.leagues.map((league, leagueIndex) =>
                <ListItem key={league.id} onPress={() => this.props.navigation.navigate('LeagueOptionsNav')}>
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
                    <H1>{this.state.sport.name}</H1>
                    <Spacer />
                    {seasonsView}
                </Content>
            </Container>
        );
    }
}

