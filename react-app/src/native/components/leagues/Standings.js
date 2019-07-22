import React from 'react';
import { Container, Content, Text, Body, Card, CardItem, View } from 'native-base';
import { Table, Row, Rows } from 'react-native-table-component';
import { StyleSheet } from 'react-native';

import Loading from '../common/Loading';

export default class Standings extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        const { league, leagueName } = this.props;

        const flexArr = [1, 8, 1, 1, 1, 1, 2];
        const tableInfo = {
            header: ['', 'Team', 'W', 'L', 'T', 'P', 'Spirit'],
            data: [],
        }

        league.standings.map((team, i) => {
            var points = parseInt(team.ties) + (parseInt(team.wins) * 2);
            tableInfo.data.push([(i + 1), team.name, team.wins, team.losses, team.ties, points, parseFloat(team.spiritAverage).toFixed(2)]);
        });

        if (league == null || league.isFetching) return <Loading />

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
                                    data={tableInfo.header}
                                    style={styles.header}
                                    textStyle={styles.headerText}
                                />
                                {
                                    tableInfo.data.map((rowData, index) => (
                                        
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
                </Content>
            </Container>
        );
    }
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: 'yellow' },
    header: { padding: 2, borderBottomWidth: 2, borderBottomColor: 'black' },
    headerText: { fontWeight: "bold"},
    text: {},
    statText: {textAlign: 'center'},
    row: { padding: 2 }
});
