import React from 'react';
import { Container, Content } from 'native-base';
import Loading from '../common/Loading';
import TeamList from './TeamList';
import ScheduleWeek from './ScheduleWeek';
import PropTypes from 'prop-types';

export default class Schedule extends React.Component {
    static propTypes = {
        league: PropTypes.object.isRequired,
    }

    constructor(props) {
        super(props);
    }

    render() {

        const { league } = this.props;

        console.log(league.scheduledMatches);

        if (league == null || league.isFetching) return <Loading />

        return (
            <Container>
                <Content>

                    <TeamList league={league} />

                    {
                        league.leagueSchedule.map((week, i) => (
                            <ScheduleWeek key={week.date.id} league={league} scheduleWeek={week} />
                        ))
                    }

                </Content>
            </Container>
        );
    }
}





