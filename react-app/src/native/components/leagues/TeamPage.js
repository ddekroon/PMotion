import React from 'react'
import PropTypes from 'prop-types';
import LeagueHelpers from '../../../utils/leaguehelpers';

import { Text, Card, CardItem, Container, Content, Header} from 'native-base';
import { Table, Row } from 'react-native-table-component';
import { StyleSheet, Image } from 'react-native';
import { connect } from 'react-redux';
import Loading from '../common/Loading';


class TeamPage extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        const { lookups } = this.props;
        const league = this.props.navigation.getParam('league');
        const team = this.props.navigation.getParam('team');
        //const {navigation} = this.props.screenProps;

        const flexArr = [6, 6, 4, 5, 5];
        const weekTable = {
            header: ['Date', 'Opponent', 'Result', 'Field', 'Time'],
            data: [],
        }

        let teamPicId = '';

        league.teams.forEach((teamName) => {
            if(teamName.id === team){
                teamPicId = teamName.picName;
            }
        });

        let teamPic = 'data.perpetualmotion.org/' + league.picLink + '/' + teamPicId + '.JPG';
        let teamMatches = [];

        //get all matches of team
        league.scheduledMatches.forEach((match) => {
            if(match.teamOneId === team || match.teamTwoId === team){
                teamMatches.push(match);
            }
        });
        teamMatches.sort((a,b) => a.dateId - b.dateId);

        //build the table with
        teamMatches.forEach((match) => {
            let opponent = '';
            let opponentStats = '';

            if(match.teamOneId === team){
                opponent = match.teamTwoId;
            }else{
                opponent = match.teamOneId;
            }

            league.teams.forEach((team) => {
                if(team.id === opponent){
                    opponentStats = '(' + team.wins + '-' + team.losses + '-' + team.ties + ')(' + parseFloat(team.spiritAverage).toFixed(2) + ')';
                }
            });

            weekTable.data.push([
                LeagueHelpers.getDate(league, match.dateId).description,
                <Text style={styles.name} onPress={() => Actions.teampage({league: league, team: opponent})}>{LeagueHelpers.getTeamName(league, opponent)} {opponentStats}</Text>,
                'TODO',
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
                            <Text style={styles.title}>{LeagueHelpers.getTeamName(league, team)} - {LeagueHelpers.getFormattedLeagueName(league)} </Text>
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
                            <Text style={styles.title}>{LeagueHelpers.getTeamName(league, team)}</Text>
                        </CardItem>   
                        <CardItem cardBody>
                            <Image source={{uri: teamPic}} style={styles.teamImage}/>
                        </CardItem>  
                    </Card> 

                </Content>
            </Container>
        );
    }
}

const mapStateToProps = state => ({
    lookups: state.lookups || {},
  });
  
export default connect(mapStateToProps)(TeamPage);

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


