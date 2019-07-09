import React from 'react';
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right} from 'native-base';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { fetchLeague } from '../../actions/leagues';
import Loading from './Loading';
import { createAppContainer, createMaterialTopTabNavigator} from 'react-navigation';
import Standings from '../components/Standings';
import Schedule from '../components/Schedule';

const leagueNavigator = (league, leagueName) => createMaterialTopTabNavigator(
    {
      Standings: props => <Standings
        league={league}
        leagueName={leagueName}
      />,
      Schedule: props => <Schedule
      league={league}
      leagueName={leagueName}
      />,
    },
  
    {
      tabBarOptions: {
        activeTintColor: 'white',
        inactiveTintColor: 'gray',
        style: {
          backgroundColor: '#303030'
        },
        indicatorStyle: {
          borderBottomColor: 'red',
          borderBottomWidth: 3,
        },
        labelStyle: {
          fontSize: 9
        },
      }
    },
  );


class LeaguePage extends React.Component { 

    constructor(props){
        super(props);
        this.props.getLeague(this.props.leagueId);
    }
 
    render() {

        const { leagues, loading, leagueId, leagueName} = this.props;

        if(loading) return <Loading />; 

        const league = leagues[leagueId];
        const LeagueNavigator = createAppContainer(leagueNavigator(league, leagueName));
        return (
          // <LeagueNavigator />\
          <Container>
          {
            league != null && !league.isFetching &&
            <LeagueNavigator/>
          }
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

/*calendar, podium, people */

