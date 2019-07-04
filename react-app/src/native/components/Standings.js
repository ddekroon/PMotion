import React from 'react';
import { H1, Container, Content, Text, Body, List, ListItem, Left, Icon, Right} from 'native-base';
import PropTypes from 'prop-types';
import Spacer from './Spacer';
import Loading from './Loading';
import { Actions } from 'react-native-router-flux';
import { Table, Row, Rows } from 'react-native-table-component';
import {StyleSheet, View} from 'react-native';

export default class Standings extends React.Component { 

    constructor(props){
        super(props);
    }
 
    render() {

        const {loading, standings, leagueName} = this.props;

        const tableInfo = {
            header: ['Rank', 'Team', 'Win', 'Loss', 'Tie', 'Points'],
            data: [],
        }

        standings.map((team, i) => {
            var points = parseInt(team.ties) + (parseInt(team.wins)*2);
            tableInfo.data.push([(i+1), team.name, team.wins, team.losses, team.ties, points]);
        });

        if(loading) return <Loading/>

        return (
            <Container>
                <Content padder>
                    <Spacer/>
                    <H1>{leagueName}</H1>

                    <View style={styles.container}>
                        <Table borderStyle={{borderWidth: 2, borderColor: '#c8e1ff'}}>
                            <Row data={tableInfo.header} style={styles.head} textStyle={styles.text}/>
                            <Rows data={tableInfo.data} textStyle={styles.text}/>
                        </Table>
                    </View>
                    <Text>{JSON.stringify(standings, null, 2)}</Text>
                </Content>
            </Container>
        );
    }
}

const styles = StyleSheet.create({
    container: { flex: 1, padding: 16, paddingTop: 30, backgroundColor: '#fff' },
    head: { height: 40, backgroundColor: '#f1f8ff' },
    text: { margin: 6 }
  });
