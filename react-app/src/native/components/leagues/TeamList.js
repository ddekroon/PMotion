import React from 'react';
import { Text, Card, CardItem, Button, Right, Icon} from 'native-base';
import { Table, Row } from 'react-native-table-component';
import { StyleSheet } from 'react-native';
import { Actions } from 'react-native-router-flux';
import Loading from '../common/Loading';
import LeagueHelpers from '../../../utils/leaguehelpers';
import PropTypes from 'prop-types';
import Spacer from '../common/Spacer';

export default class Schedule extends React.Component {
    static propTypes = {
        league: PropTypes.object.isRequired,
    }

    constructor(props) {
        super(props);
    }

    render() {

        const { league } = this.props;

        const flexArr = [2, 7];
        const teamTable = {
            header: ['Team #', 'Name'],
            data: [],
        }

        league.teams.map((team, i) => {
            teamTable.data.push([
                <Text style={{textAlign: 'center'}}>{team.numInLeague}</Text>,
                <Button iconRight style={styles.button} onPress={() => Actions.teampage({league: league, team: team.id})}>
                    <Text style={styles.name}>{team.name}</Text>
                    <Icon style={{marginRight:5}} name="arrow-forward" style={{color: 'white'}}/>
                </Button>
            ]);
        });

        if (league == null || league.isFetching) return <Loading />

        return (
            <Card>
                <CardItem header>
                    <Text>{LeagueHelpers.getFormattedLeagueName(league)}</Text>
                </CardItem>
                <CardItem cardBody style={styles.cardItem}>
                    <Table style={styles.table} borderStyle={styles.tableborderstyle}>
                        <Row
                            flexArr={flexArr}
                            data={teamTable.header}
                            style={styles.header}
                            textStyle={styles.headerText}
                        />
                        {
                            teamTable.data.map((rowData, index) => (
                                <Row
                                    key={index}
                                    flexArr={flexArr}
                                    data={rowData}
                                    style={[styles.row, index % 2 == 1 && { backgroundColor: '#e6e6e6' }]}
                                    textStyle={styles.text}
                                />
                            ))
                        }
                    </Table>
                </CardItem>
            </Card>
        );
    }
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: 'yellow' },
    header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
    headerText: { fontWeight: "bold" },
    text: {},
    row: { padding: 2 },
    table: { flex: 1, marginBottom: 10 },
    tableborderstyle: { borderWidth: 0, borderColor: "transparent" },
    cardItem: {padding: 10},
    button: {width: 265, height: 40},
    name: {fontSize: 14}
});



