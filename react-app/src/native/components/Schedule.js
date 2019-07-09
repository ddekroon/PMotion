import React from 'react';
import { Container, Content, Text, Body, Card, CardItem, View, H1 } from 'native-base';
import PropTypes from 'prop-types';
import Loading from './Loading';
import { Table, Row, Rows } from 'react-native-table-component';
import { StyleSheet } from 'react-native';

export default class Schedule extends React.Component {

    constructor(props){
        super(props);
    }

    //scheduledMatches array
    //teamOneId
    //feildId
    //matchTime
    //dateId


    //teams
    //numInLeague
    //id

    render(){

        const {loading, league, leagueName} = this.props;

        const flexArr = [2, 7];
        const teamTable = {
            header: ['Team #', 'Name'],
            data: [],
        }

        league.teams.map((team, i) => {
            teamTable.data.push([team.numInLeague, team.name]);
        });

        if (loading) return <Loading/>

        return (
            <Container>
            <Content padder>
                <Card>
                    <CardItem header>
                        <Text>{leagueName}</Text>
                    </CardItem>
                    <CardItem cardBody style={{ padding: 10 }}>
                        <Table style={{ flex: 1, marginBottom: 10 }} borderStyle={{ borderWidth: 0, borderColor: "transparent" }}>
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

                {
                    league != null && !league.isFetching &&
                    <Content><Text>{JSON.stringify(league, null, 2)}</Text></Content>
                }

            </Content>
            </Container>
        );
    }
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: 'yellow' },
    header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
    headerText: { fontWeight: "bold" },
    text: {},
    row: { padding: 2 }
});