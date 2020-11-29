import React from 'react'
import PropTypes from 'prop-types';
import LeagueHelpers from '../../../utils/leaguehelpers';
import DateTimeHelpers from '../../../utils/datetimehelpers';
import Enums from '../../../constants/enums';

import { Text, Container, Content, Button, Icon } from 'native-base';
import { Table, Row } from 'react-native-table-component';
import { StyleSheet, Image, View } from 'react-native';
import { connect } from 'react-redux';
import Loading from '../common/Loading';
import {fetchTeam } from '../../../actions/teams';

/**
 * TODO
 * - check if teamphoto exists
 */

class TeamPage extends React.Component {
    state = {
        teamId: -1
    }

    constructor(props) {
        super(props);
        this.state.teamId = this.props.route?.params?.team ?? -1;
        props.fetchTeam(this.state.teamId);
    }

    getMatchResult = (oppTeamId, dateId, submissions) => 
        //get the game result
        submissions.filter(x => x.oppTeamId == oppTeamId && x.dateId == dateId).map(x => 
            Object.keys(Enums.matchResult).find(y => Enums.matchResult[y].val == x.result) ?? ['']
        )[0] ?? ''

    generateMatchesData = (league, team) => {
        let { lookups } = this.props

        let hideSpirit = LeagueHelpers.checkHideSpirit(league)
        let weekData = [];
        let teamMatches = league.scheduledMatches.filter((match) => match.teamOneId === team.id || match.teamTwoId === team.id)

        //sort score submissions to match scheduled matches
        team.scoreSubmissions.sort((a,b) => a.dateId - b.dateId);

        teamMatches.sort((a,b) => a.dateId - b.dateId).forEach((match) => {

            let oppTeamId = match.teamOneId === team.id ? match.teamTwoId : match.teamOneId;
            let oppStats = league.teams.filter(x => x.id === oppTeamId).map(x => 
                !hideSpirit
                    ? x.wins + '-' + x.losses + '-' + x.ties + ' | ' + parseFloat(x.spiritAverage).toFixed(2)
                    : x.wins + '-' + x.losses + '-' + x.ties
            )[0] ?? '';

            var gameResult = this.getMatchResult(oppTeamId, match.dateId, team.scoreSubmissions)

            //fill the table
            weekData.push([
                <View style={styles.cell}>
                    <Text>{DateTimeHelpers.getShortDate(LeagueHelpers.getDate(league, match.dateId).description)}</Text>
                    <Text style={styles.name} onPress={() => this.props.navigation.push('Maps', {venue: lookups.venues[match.fieldId].name})}>
                        {lookups.venues[match.fieldId].name}
                    </Text>
                </View>,
                <View style={styles.cell}>
                    <Text>
                        {match.teamOneId === team.id ? '@ ' : 'vs '}
                        <Text style={styles.name} onPress={() => this.props.navigation.push('Team',{league: league, team: oppTeamId, title: LeagueHelpers.getTeamName(league, oppTeamId) })}>
                            {LeagueHelpers.getTeamName(league, oppTeamId)} 
                        </Text>
                    </Text>
                    <Text>{oppStats}</Text>
                </View>,
                <View style={styles.cell}>
                    <Text style={{textAlign:'right'}}>{gameResult.length > 0 ? gameResult : LeagueHelpers.convertMatchTime(match.matchTime)}</Text>
                </View>
            ]);
        });

        return weekData;
    }

    render() {

        const { teams } = this.props;
        const league = this.props.route?.params?.league ?? -1;
        const team = teams[this.state.teamId];

        if (league == null || league.isFetching) return <Loading />
        if(team == null || team.isFetching) return <Loading />

        return (
            <Container>
                <Content padder>
                    <Table style={styles.table} borderStyle={styles.tableborderstyle}>
                        {
                            this.generateMatchesData(league, team).map((rowData, index) => (
                                <Row
                                    key={index}
                                    flexArr={[2, 2, 1]}
                                    data={rowData}
                                    style={[styles.row, index % 2 == 1 && { backgroundColor: '#e6e6e6' }]}
                                    textStyle={[styles.text, rowData[2] === '' && {fontWeight: 'bold', fontSize: 20}]}
                                />
                            ))
                        }
                    </Table>

                    <Button light iconRight style={{...styles.playoffButton, alignSelf: 'center' }} onPress={() => this.props.navigation.push('Schedule', {leagueId: league.id, title: LeagueHelpers.getFormattedLeagueName(league) })}>
                        <Text>For playoffs please see the full schedule</Text>
                        <Icon style={{ marginRight: 5 }} name="arrow-forward" style={{ color: 'black' }}/>
                    </Button>

                    {league.picLink != null && team.picName != null && team.isPic && 
                        <Image source={{uri: 'https://data.perpetualmotion.org/' + league.picLink + '/' + team.picName + '.JPG'}} style={styles.teamImage} />
                    }

                </Content>
            </Container>
        );
    }
}

const mapStateToProps = state => ({
    lookups: state.lookups || {},
    teams: state.teams || {},
});

const mapDispatchToProps = {
    fetchTeam: fetchTeam
}
  
export default connect(mapStateToProps, mapDispatchToProps)(TeamPage);

const styles = StyleSheet.create({
    playoffButton: { marginBottom: 20 },
    row: { padding: 2 },
    cell: { padding: 2 },
    table: { flex: 1, marginBottom: 10 },
    tableborderstyle: { borderWidth: 0, borderColor: "transparent" },
    cardItem: {padding: 10},
    title: {fontSize: 18},
    name: {color: 'red'},
    teamImage: {width: null, height: 300, flex: 1, resizeMode: 'cover'}
}); 


