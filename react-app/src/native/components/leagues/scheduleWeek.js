import React from 'react'
import PropTypes from 'prop-types';
import LeagueHelpers from '../../../utils/leaguehelpers';

import { Text, Card, CardItem } from 'native-base';
import { Table, Row } from 'react-native-table-component';
import { StyleSheet } from 'react-native';
import { connect } from 'react-redux';
import Loading from '../common/Loading';

class ScheduleWeek extends React.Component {
    static propTypes = {
        schedule: PropTypes.object.isRequired,
        league: PropTypes.object.isRequired,
    }

    constructor(props) {
        super(props);
    }

    /**
     * need to sort games by the location
     * add locations names to schedule
     * add bold to the times - change the schedule object
     * add names to schedule 
     */

    render() {

        const { schedule, league, lookups } = this.props;

        const flexArr = [4, 2, 2, 2];
        const weekTable = {
            header: ['Field', 'Dark', '', 'White'],
            data: [],
        }

        //week1: { date: {}, matchTimes: [], matches: [{venue, time, team1, team2}]},
        let prevTime = '';
        schedule.matches.forEach((match) => {
            if (prevTime != match.time) {
                weekTable.data.push([LeagueHelpers.convertMatchTime(match.time), '', '', '']);
            }
            weekTable.data.push([match.venue, LeagueHelpers.getNumInLeague(league, match.team1), 'vs', LeagueHelpers.getNumInLeague(league, match.team2)]);
            prevTime = match.time;
        });

        if (league == null || league.isFetching) return <Loading />

        return (
            <Card>
                <CardItem header>
                    <Text>{schedule.date.description} - Week {schedule.date.weekNumber}</Text>
                </CardItem>
                <CardItem cardBody style={{ padding: 10 }}>
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
});

const mapStateToProps = state => ({
    lookups: state.lookups || {},
  });
  
export default connect(mapStateToProps)(ScheduleWeek);
