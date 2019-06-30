import React from 'react';
import PropTypes from 'prop-types';
import {
	Item, Label, Input, Picker, Textarea, Card, CardItem, Body, Text, Badge, Icon
} from 'native-base';
import { Col, Row, Grid } from 'react-native-easy-grid';
import Loading from './Loading';

class ScoreReporterMatch extends React.Component {
	static propTypes = {
		loading: PropTypes.bool.isRequired,
		matchNum: PropTypes.number.isRequired,
		updateMatchHandler: PropTypes.func.isRequired
	}

	constructor(props) {
		super(props);

		this.state = {
			oppTeamId: '',
			results: [],
			spiritScore: '',
			comment: ''
		};

		this.handleChange = this.handleChange.bind(this);
	}

	handleChange = (name, val) => {
		this.setState({
			[name]: val,
		});

		//this.props.updateMatchHandler(matchNum, this.state);
	}

	render() {
		const { loading, matchNum } = this.props;

		return (
			<Card>
				<CardItem>
					<Body>
						<Badge><Text>Match {matchNum + 1}</Text></Badge>

						<Item fixedLabel>
							<Label>
								Opponent
							</Label>
							<Input />
						</Item>

						<Item fixedLabel>
							<Label>
								Result 1
							</Label>
							<Input />
						</Item>

						<Grid>
							<Col>
								<Item fixedLabel>
									<Label>
										We Got
									</Label>
									<Input />
								</Item>
							</Col>
							<Col>
								<Item fixedLabel>
									<Label>
										They Got
									</Label>
									<Input />
								</Item>
							</Col>
						</Grid>

						<Item picker>
							<Picker
								note={false}
								mode="dropdown"
								iosIcon={<Icon name="arrow-down" />}
								style={{ flex: 1 }}
								selectedValue={this.state.spiritScore}
								placeholder="Spirit Score"
								onValueChange={(val, index) => {
									this.handleChange('spiritScore', val)
								}}
							>
								<Picker.Item key={0} label="Spirit Score" value='' />
								<Picker.Item key={1} label="1" value={1} />
								<Picker.Item key={2} label="1.5" value={1.5} />
								<Picker.Item key={3} label="2" value={2} />
								<Picker.Item key={4} label="2.5" value={2.5} />
								<Picker.Item key={5} label="3" value={3} />
								<Picker.Item key={6} label="3.5" value={3.5} />
								<Picker.Item key={7} label="4" value={4} />
								<Picker.Item key={8} label="4.5" value={4.5} />
								<Picker.Item key={9} label="5" value={5} />
							</Picker>
						</Item>

						<Item regular style={{ marginTop: 10 }}>
							<Textarea
								style={{ flex: 1, paddingTop: 5, paddingBottom: 5 }}
								rowSpan={3}
								placeholder="Comments"
								placeHolderTextStyle={{ color: "#d3d3d3" }}
							/>
						</Item>
					</Body>
				</CardItem>
			</Card>
		);
	}
}

export default ScoreReporterMatch;
