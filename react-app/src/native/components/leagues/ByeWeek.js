import React from 'react';
import { Container, Content, Text, Card, CardItem } from 'native-base';
import PropTypes from 'prop-types';

export default class ByeWeek extends React.Component {
    static propTypes = {
        scheduleWeek: PropTypes.object.isRequired,
    }

    constructor(props) {
        super(props);
    }

    render() {

        const { scheduleWeek } = this.props;

        return (
            <Card>
                <CardItem header>
                    <Text> BYE WEEK: {scheduleWeek.date.description} - Week {scheduleWeek.date.weekNumber}</Text>
                </CardItem>
            </Card>
        );
    }
}
