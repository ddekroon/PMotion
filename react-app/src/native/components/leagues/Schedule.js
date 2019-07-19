import React from 'react';
import { Container, Content, Text, Body, Card, CardItem, View, H1 } from 'native-base';
import { Table, Row, Rows } from 'react-native-table-component';
import { StyleSheet } from 'react-native';
import Loading from '../common/Loading';
import TeamList from '../leagues/TeamList';
import helpers from '../../../utils/leaguehelpers';

export default class Schedule extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        /*
        The top of this component is the list of teams, which should be split into it's own component.
        Since you have the list of teams & team nums now (which BTW team num in league is an attribute on teams)
            you should be able to send through the teams to that component and render accordingly.

        Next you'll want to organize the list of schedules matches by week. The structure of the object should be:
        {
            week1: { date: {}, matchTimes: [], matches: [{venue, time, team1, team2}]},
            week2: { date: {}, matchTimes: [], matches: [{venue, time, team1, team2}]},
            week3: { date: {}, matchTimes: [], matches: [{venue, time, team1, team2}]},
            etc
        }

        Now that you have your object you can iterate them in the render section. 
        For each week render another ScheduleWeek object (you'll have to create a custom object, much the same way as score reporter matches).
        This gives you maximum flexibility with your ScheduleWeek object. Each object should look like a section of the schedules online
        where the top line is the date and week number, second line is the list of game times, then all subsequent lines are venues and matches.

        I do use a table online so you can try using one here, if we figure out we need more flexibility for styling we can deal with it later.
        The only other thing you need to worry about is if there are no scheduled matches for a week. If so just render a Text element with
        bolded text: [DATE] **No Game.
        */

        const { league, leagueName, lookups } = this.props;

        const flexArr = [2, 7];
        const teamTable = {
            header: ['Team #', 'Name'],
            data: [],
        }

        const scheduleWeeks = [];

        league.teams.map((team, i) => {
            teamTable.data.push([team.numInLeague, team.name]);
        });

        /*
        league.scheduledMatches.map((match, i) => {
            let date = helpers.getDate(league, match.dateId);
            let matchTimes = helpers.getMatchTimes(league, match.dateId);
            let matches = helpers.getMatches(league, match.dateId);
            let week = {
                week: date.weekNumber,
                date: date.description,
                matchTimes: matchTimes,
                matches: matches
            };

            scheduleWeeks.push(week);
        });

        console.log(scheduleWeeks);
        */
        

        if (league == null || league.isFetching) return <Loading />

        return (
            <Container>

                <TeamList league={league} leagueName={leagueName} lookups={lookups}/>

            </Container>
        );
    }
}







