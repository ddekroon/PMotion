import React from 'react';
import PropTypes from 'prop-types';
import {
	Item, Label, Input, Picker, Textarea, Card, CardItem, Body, Text, Badge, Icon
} from 'native-base';
import { Col, Row, Grid } from 'react-native-easy-grid';
import Enums from '../../constants/enums'

class ScoreReporterMatch extends React.Component {
	static propTypes = {
		loading: PropTypes.bool.isRequired,
		matchNum: PropTypes.number.isRequired,
		updateMatchHandler: PropTypes.func.isRequired,
		league: PropTypes.object.isRequired
	}

	constructor(props) {
		super(props);

		this.state = {
			oppTeamId: '',
			results: [
				{
					result: Enums.matchResult.Error.val,
					scoreUs: '',
					scoreThem: ''
				},
				{
					result: Enums.matchResult.Error.val,
					scoreUs: '',
					scoreThem: ''
				}
			],
			spiritScore: '',
			comment: ''
		};

		this.handleChange = this.handleChange.bind(this);
		this.handleScoreChange = this.handleScoreChange.bind(this);
	}

	handleChange = (name, val) => {
		const { matchNum } = this.props;

		this.setState({
			[name]: val,
		});

		this.props.updateMatchHandler(matchNum, this.state);
	}

	handleScoreChange = (gameNum, name, val) => {
		const { matchNum } = this.props;

		var scoreVals = this.state.results;

		scoreVals[gameNum][name] = val;

		this.setState({
			'results': scoreVals,
		});

		this.props.updateMatchHandler(matchNum, this.state);
	}

	render() {
		const { loading, matchNum, league } = this.props;
		const { oppTeamId, results, spiritScore, comment } = this.state;
		const { handleScoreChange } = this;

		function getScorePicker(gameNum, label, stateScoreKey, selectedValue, maxPoints) {
			return <Item picker>
				<Picker
					note={false}
					mode="dropdown"
					iosIcon={<Icon name="arrow-down" />}
					style={{ flex: 1 }}
					selectedValue={selectedValue}
					placeholder={label}
					onValueChange={(val, idx) => {
						handleScoreChange(gameNum, stateScoreKey, val)
					}}
				>
					<Picker.Item key={0} label={label} value='' />
					{
						Array.apply(null, { length: maxPoints }).map((element, index) => {
							return <Picker.Item key={index} label={index.toString()} value={index} />
						})
					}
				</Picker>
			</Item>
		}

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

						{
							Array.apply(null, new Array(parseInt(league.numGamesPerMatch, 10))).map((e, gameIndex) => {
								var gameString = "Result " + (league.numGamesPerMatch > 1 ? (gameIndex + 1) : '');
								return <Item picker>
									<Picker
										note={false}
										mode="dropdown"
										iosIcon={<Icon name="arrow-down" />}
										style={{ flex: 1 }}
										selectedValue={results[gameIndex].result}
										placeholder={gameString}
										onValueChange={(val, idx) => {
											handleScoreChange(gameIndex, 'result', val)
										}}
									>
										<Picker.Item key={0} label={gameString} value='' />
										{
											Object.values(Enums.matchResult).filter((curResult) => {
												return !(curResult.val == Enums.matchResult.Error.val
													|| curResult.val == Enums.matchResult.Tied.val && !league.isTies
													|| curResult.val == Enums.matchResult.Practice.val && !league.isPracticeGames
													|| curResult.val == Enums.matchResult.Cancelled.val && !league.isShowCancelOption
												);
											}).map((curResult) => {
												return <Picker.Item key={curResult.val} label={curResult.text} value={curResult.val} />
											})
										}

									</Picker>
								</Item> || (
										league.isAskForScores &&
										<Grid>
											<Col>
												{getScorePicker(gameIndex, 'We Got', 'scoreUs', results[gameIndex].scoreUs, parseInt(league.maxPointsPerGame, 10))}
											</Col>
											<Col>
												{getScorePicker(gameIndex, 'They Got', 'scoreThem', results[gameIndex].scoreThem, parseInt(league.maxPointsPerGame, 10))}
											</Col>
										</Grid>
									)
							})
						}



						<Item picker>
							<Picker
								note={false}
								mode="dropdown"
								iosIcon={<Icon name="arrow-down" />}
								style={{ flex: 1 }}
								selectedValue={spiritScore}
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
								onChangeText={v => this.handleChange('comment', v)}
								value={comment}
							/>
						</Item>
					</Body>
				</CardItem>
			</Card>
		);
	}
}

export default ScoreReporterMatch;
