import React from 'react'
import PropTypes from 'prop-types';
import LeagueHelpers from '../../../utils/leaguehelpers';

import { Text, Card, CardItem, Container, Content, Header} from 'native-base';
import { Table, Row } from 'react-native-table-component';
import { StyleSheet, Image } from 'react-native';
import { connect } from 'react-redux';
import Loading from '../common/Loading';
import {fetchTeam, resetTeamStore} from '../../../actions/teams';


class TeamPage extends React.Component {
    state = {
        teamId: -1
    }

    constructor(props) {
        super(props);
        this.state.teamId = this.props.navigation.getParam('team');
        props.fetchTeam(this.state.teamId);
    }

    checkHideSpirit = () => {

        const league = this.props.navigation.getParam('league');
        let curDay = new Date().getDay();
        let timeOfDay = new Date().getHours();
        let dayHide = parseInt(league.dayNumber);
        let dayShow = dayHide + parseInt(league.numDaysSpiritHidden);
    
        if(dayShow > 7){
            dayShow = dayShow % 7;
        }
    
        if(curDay == dayHide){
            return timeOfDay >= league.hideSpiritHour;
        }
    
        if(curDay == dayShow){
            return !(timeOfDay >= league.showSpiritHour);
        }
    
        return (curDay > dayHide && curDay < dayShow || curDay == 1 && dayHide == 7); 
    }

    render() {

        const { lookups, teams } = this.props;
        const league = this.props.navigation.getParam('league');
        const teamId = this.state.teamId;
        const team = teams[teamId];
        let teamPicId = '';
        let teamMatches = [];
        const flexArr = [6, 6, 4, 5, 5];
        const weekTable = {
            header: ['Date', 'Opponent', 'Result', 'Field', 'Time'],
            data: [],
        }

        //sort score submissions to match scheduled matches
        team.scoreSubmissions.sort((a,b) => a.dateId - b.dateId);

        //get the teams picture name
        league.teams.forEach((team) => {
            if(team.id === teamId){
                teamPicId = team.picName;
            }
        });

        //build link to team pic
        let teamPic = 'data.perpetualmotion.org/' + league.picLink + '/' + teamPicId + '.JPG';

        //get all matches of team and sort
        league.scheduledMatches.forEach((match) => {
            if(match.teamOneId === teamId || match.teamTwoId === teamId){
                teamMatches.push(match);
            }
        });
        teamMatches.sort((a,b) => a.dateId - b.dateId);

        //build the table 
        teamMatches.forEach((match) => {
            let opponent = '';
            let opponentStats = '';
            let gameResult = '';

            if(match.teamOneId === teamId){
                opponent = match.teamTwoId;
            }else{
                opponent = match.teamOneId;
            }

            //get the opponents stats
            league.teams.forEach((team) => {
                if(team.id === opponent){
                    if(this.checkHideSpirit() == false){
                        opponentStats = '(' + team.wins + '-' + team.losses + '-' + team.ties + ')(' + parseFloat(team.spiritAverage).toFixed(2) + ')';
                    }else{
                        opponentStats = '(' + team.wins + '-' + team.losses + '-' + team.ties + ')';
                    }
                }
            });

            //get the game result
            team.scoreSubmissions.forEach((sub) => {
                if(sub.oppTeamId == opponent && sub.dateId == match.dateId){
                    if(sub.result == '1'){
                        gameResult = 'Won';
                    }else if(sub.result == '2'){
                        gameResult = 'Lost';
                    }else if(sub.result == '3'){
                        gameResult = 'Tie';
                    }else{
                        gameResult = 'N/A'
                    }
                }
            });

            //fill the table
            weekTable.data.push([
                LeagueHelpers.getDate(league, match.dateId).description,
                <Text style={styles.name} onPress={() => this.props.navigation.push('Team',{league: league, team: opponent})}>{LeagueHelpers.getTeamName(league, opponent)} {opponentStats}</Text>,
                gameResult,
                lookups.venues[match.fieldId].name,
                LeagueHelpers.convertMatchTime(match.matchTime)
            ]);
        });


        if (league == null || league.isFetching) return <Loading />

        return (
            <Container>
                <Content>    
                    <Card>
                        <CardItem header>
                            <Text style={styles.title}>{LeagueHelpers.getTeamName(league, teamId)} - {LeagueHelpers.getFormattedLeagueName(league)} </Text>
                        </CardItem>
                        <CardItem cardBody style={styles.cardItem}>
                            <Table style={styles.table} borderStyle={styles.tableborderstyle}>
                                <Row
                                    flexArr={flexArr}
                                    data={weekTable.header}
                                    style={styles.header}
                                    textStyle={styles.headerText}
                                />
                                {
                                    weekTable.data.map((rowData, index) => (
                                        <Row
                                            key={index}
                                            flexArr={flexArr}
                                            data={rowData}
                                            style={[styles.row, index % 2 == 1 && { backgroundColor: '#e6e6e6' }]}
                                            textStyle={[styles.text, rowData[2] === '' && {fontWeight: 'bold', fontSize: 20}]}
                                        />
                                    ))
                                }
                            </Table>
                        </CardItem>
                    </Card>


                    <Card>
                        <CardItem header>
                            <Text style={styles.title}>{LeagueHelpers.getTeamName(league, teamId)}</Text>
                        </CardItem>   
                        <CardItem cardBody>
                            <Image source={require('../../../images/app-icon.png')} style={styles.teamImage}/>
                        </CardItem>  
                    </Card> 

                </Content>
            </Container>
        );
    }
}

//{uri: teamPic}

const mapStateToProps = state => ({
    lookups: state.lookups || {},
    teams: state.teams || {},
});

const mapDispatchToProps = {
    fetchTeam: fetchTeam
}
  
export default connect(mapStateToProps, mapDispatchToProps)(TeamPage);

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: 'yellow' },
    header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
    headerText: { fontWeight: "bold" },
    text: {},
    row: { padding: 2 },
    table: { flex: 1, marginBottom: 10 },
    tableborderstyle: { borderWidth: 0, borderColor: "transparent" },
    cardItem: {padding: 10},
    title: {fontSize: 18},
    name: {color: 'red'},
    teamImage: {width: null, height: 300, flex: 1, resizeMode: 'cover'}
});


