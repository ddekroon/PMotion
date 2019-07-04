import React from 'react';
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right} from 'native-base';
import PropTypes from 'prop-types';
import Spacer from './Spacer';
import { connect } from 'react-redux';
import { fetchLeague } from '../../actions/leagues';
import Loading from './Loading';
import { Actions } from 'react-native-router-flux';

class LeaguePage extends React.Component { 

    constructor(props){
        super(props);
        this.props.getLeague(this.props.leagueId);
    }
 
    render() {

        const { leagues, loading, leagueId, leagueName } = this.props;

        let league = leagues[leagueId];

        if(loading) return <Loading />; 

        return (
            <Container>
                <Content padder>
                    <Spacer/>
                    <H1>{leagueName}</H1>
                    <Spacer/>
                    <List>
                        <ListItem icon>
                            <Left>
                                <Icon name="calendar" />
                            </Left>
                            <Body>
                                <Text>Schedule</Text>
                            </Body>
                            <Right>
                                <Icon name = "arrow-forward"/>
                            </Right>
                        </ListItem>
                    </List>

                    <List>
                        <ListItem icon onPress={()=> Actions.standings({standings: league.standings, leagueName: leagueName})}>
                            <Left>
                                <Icon name="podium" />
                            </Left>
                            <Body>
                                <Text>Standings</Text>
                            </Body>
                            <Right>
                                <Icon name = "arrow-forward"/>
                            </Right>
                        </ListItem>
                    </List>

                    <List>
                        <ListItem icon>
                            <Left>
                                <Icon name="people" />
                            </Left>
                            <Body>
                                <Text>Team Pages</Text>
                            </Body>
                            <Right>
                                <Icon name = "arrow-forward"/>
                            </Right>
                        </ListItem>
                    </List>
                </Content>
            </Container>
        );
        
    }
}

const mapStateToProps = state => ({
    leagues: state.leagues || {},
  });
  
  const mapDispatchToProps = {
    getLeague: fetchLeague,
  };
  
export default connect(mapStateToProps, mapDispatchToProps)(LeaguePage);



