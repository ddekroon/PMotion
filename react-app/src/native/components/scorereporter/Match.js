import React from 'react';
import PropTypes from 'prop-types';

import { Item, Label, Input, Picker, Textarea, Card, CardItem, Body, Text, Badge, Icon } from 'native-base';
import { Col, Row, Grid } from 'react-native-easy-grid';

import TeamPicker from '../common/TeamPicker'
import Messages from '../common/Messages';

import Enums from '../../../constants/enums'
import ValidationHelpers from '../../../utils/validationhelpers';

class ScoreReporterMatch extends React.Component {
	static propTypes = {
		matchNum: PropTypes.number.isRequired,
		updateMatchHandler: PropTypes.func.isRequired,
		matchSubmission: PropTypes.object.isRequired,
		league: PropTypes.object.isRequired,
		curTeamId: PropTypes.string.isRequired
	}

	constructor(props) {
		super(props);

		this.handleChange = this.handleChange.bind(this);
		this.handleScoreChange = this.handleScoreChange.bind(this);
	}

	handleChange = (name, val) => {
		const { matchNum, matchSubmission } = this.props;

		var newMatchSubmission = {
			...matchSubmission,
			[name]: val
		}

		this.props.updateMatchHandler(matchNum, newMatchSubmission);
	}

	handleScoreChange = (gameNum, name, val) => {
		const { matchNum, matchSubmission } = this.props;

		var newGameResults = matchSubmission.results.map((game, index) => {
			if (index != gameNum) {
				return game
			}

			return {
				...game,
				[name]: val
			}
		})

		var newMachSubmission = {
			...matchSubmission,
			results: newGameResults
		};

		this.props.updateMatchHandler(matchNum, newMachSubmission);
	}

	getScorePicker = (gameNum, label, stateScoreKey, selectedValue, maxPoints) => {

		var options = [{placeholder: true }].concat(Array.apply(null, { length: maxPoints }))

		return <Item picker>
			<Picker
				note={false}
				mode="dropdown"
				iosIcon={<Icon name="ios-arrow-down" />}
				style={{ flex: 1 }}
				textStyle={{ fontWeight: 'normal' }}
				selectedValue={selectedValue}
				placeholder={label}
				onValueChange={(val, idx) => {
					this.handleScoreChange(gameNum, stateScoreKey, val)
				}}
			>
				{
					options.map((element, index) => {
						if(element != null)
						{
							return <Picker.Item key={0} label={label} value='' />
						}
						return <Picker.Item key={index} label={index.toString()} value={index - 1} />
					})
				}
			</Picker>
		</Item>
	}

	render() {
		const { matchNum, league, curTeamId } = this.props;
		const { oppTeamId, results, spiritScore, comment } = this.props.matchSubmission;

		var resultOptions = [{ placeholder: true }].concat(Object.values(Enums.matchResult))

		return (
			<Card>
				<CardItem>
					<Body>
						<Badge><Text>Match {matchNum + 1}</Text></Badge>

						<TeamPicker
							label="Opponent"
							loading={false}
							teams={league.teams != null ? league.teams : []}
							curTeamId={oppTeamId}
							excludeTeamId={curTeamId}
							onTeamUpdated={(val) => this.handleChange('oppTeamId', val)}
						/>

						{
							Array.apply(null, new Array(parseInt(league.numGamesPerMatch, 10))).map((e, gameIndex) => {
								var gameString = "Result " + (league.numGamesPerMatch > 1 ? (gameIndex + 1) : '');
								var obj = [];
								obj.push(
									<Item
										key={'oppTeamId' + gameIndex}
										picker
										style={{flex:1, width:'100%'}}
										error={!ValidationHelpers.isValidGameResult(results[gameIndex].result)}
									>
										<Picker
											note={false}
											mode="dropdown"
											iosIcon={<Icon name="ios-arrow-down" />}
											style={{ flex: 1 }}
											textStyle={{ fontWeight: 'normal' }}
											selectedValue={results[gameIndex].result}
											placeholder={gameString}
											onValueChange={(val, idx) => {
												this.handleScoreChange(gameIndex, 'result', val)
											}}
										>
											{
												resultOptions.filter((curResult) => {
													return curResult.placeholder || !(curResult.val == Enums.matchResult.Error.val
														|| curResult.val == Enums.matchResult.Tied.val && !league.isTies
														|| curResult.val == Enums.matchResult.Practice.val && !league.isPracticeGames
														|| curResult.val == Enums.matchResult.Cancelled.val && !league.isShowCancelOption
													);
												}).map((curResult) => {
													if(curResult.placeholder)
													{
														return <Picker.Item key={0} label={gameString} value='' />
													}
													return <Picker.Item key={curResult.val} label={curResult.text} value={curResult.val} />
												})
											}

										</Picker>
									</Item>
								)

								if (league.isAskForScores) {
									obj.push(
										<Grid key={'scores' + gameIndex}>
											<Col>
												{this.getScorePicker(gameIndex, 'We Got', 'scoreUs', results[gameIndex].scoreUs, parseInt(league.maxPointsPerGame, 10))}
											</Col>
											<Col>
												{this.getScorePicker(gameIndex, 'They Got', 'scoreThem', results[gameIndex].scoreThem, parseInt(league.maxPointsPerGame, 10))}
											</Col>
										</Grid>
									)
								}

								return obj;
							})
						}

						<Item picker error={spiritScore == ''} style={{flex:1, width:'100%'}}>
							<Picker
								note={false}
								mode="dropdown"
								iosIcon={<Icon name="ios-arrow-down" />}
								style={{ flex: 1 }}
								textStyle={{ fontWeight: 'normal' }}
								selectedValue={spiritScore}
								placeholder="Spirit Score"
								onValueChange={(val, index) => {
									this.handleChange('spiritScore', val)
								}}
							>
								<Picker.Item label="Spirit Score" value='' />
								<Picker.Item label="5" value={5} />
								<Picker.Item label="4.5" value={4.5} />
								<Picker.Item label="4" value={4} />
								<Picker.Item label="3.5" value={3.5} />
								<Picker.Item label="3" value={3} />
								<Picker.Item label="2.5" value={2.5} />
								<Picker.Item label="2" value={2} />
								<Picker.Item label="1.5" value={1.5} />
								<Picker.Item label="1" value={1} />
							</Picker>
						</Item>

						<Item
							regular
							style={{ marginTop: 10 }}
							error={spiritScore != '' && parseFloat(spiritScore) < 4 && comment.length < 3}
						>
							<Textarea
								style={{ flex: 1, paddingTop: 5, paddingBottom: 5 }}
								rowSpan={3}
								placeholder="Comments"
								placeHolderTextStyle={{ color: "#d3d3d3" }}
								onChangeText={v => this.handleChange('comment', v)}
								value={comment}
							/>
						</Item>

						{
							spiritScore != '' && parseFloat(spiritScore) < 4 && comment.length < 4 &&
							<Item style={{ marginTop: 10 }}>
								<Messages
									type="error"
									message="A comment is required when a spirit score of 3.5 or less is given"
								/>
							</Item>
						}
					</Body>
				</CardItem>
			</Card>
		);
	}
}

export default ScoreReporterMatch;
