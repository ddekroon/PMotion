import React from 'react';
import { Container, Content } from 'native-base';
import Loading from '../common/Loading';
import TeamList from './TeamList';
import ScheduleWeek from './ScheduleWeek';
import ByeWeek from './ByeWeek';
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

        if (league == null || league.isFetching) return <Loading />

        return (
            <Container>
                <Content>

                    <TeamList league={league} />

                    {   
                        league.leagueSchedule.map((week, i) => {
                            if(Object.keys(week.times).length === 0){
                                return <ByeWeek key={week.date.id} scheduleWeek={week} />
                            }else{
                                return <ScheduleWeek key={week.date.id} league={league} scheduleWeek={week} />
                            }
                        })
                    }

                </Content>
            </Container>
        );
    }
}





