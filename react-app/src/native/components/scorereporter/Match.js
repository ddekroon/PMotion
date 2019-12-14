import React from 'react'
import PropTypes from 'prop-types'

import {
  Item,
  Picker,
  Textarea,
  Card,
  CardItem,
  Body,
  Text,
  Badge,
  Icon
} from 'native-base'
import { Col, Grid } from 'react-native-easy-grid'

import TeamPicker from './TeamPicker'
import ResultPicker from './ResultPicker'
import SpiritPicker from './SpiritPicker'
import Messages from '../common/Messages'

import Enums from '../../../constants/enums'

class ScoreReporterMatch extends React.Component {
  static propTypes = {
    matchNum: PropTypes.number.isRequired,
    updateMatchHandler: PropTypes.func.isRequired,
    matchSubmission: PropTypes.object.isRequired,
    league: PropTypes.object.isRequired,
    curTeamId: PropTypes.string.isRequired,
    validate: PropTypes.bool.isRequired
  }

  constructor(props) {
    super(props)

    this.handleChange = this.handleChange.bind(this)
    this.handleScoreChange = this.handleScoreChange.bind(this)
  }

  handleChange = (name, val) => {
    const { matchNum, matchSubmission } = this.props

    var newMatchSubmission = {
      ...matchSubmission,
      [name]: val
    }

    this.props.updateMatchHandler(matchNum, newMatchSubmission)
  }

  handleScoreChange = (gameNum, name, val) => {
    const { matchNum, matchSubmission } = this.props

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
    }

    this.props.updateMatchHandler(matchNum, newMachSubmission)
  }

  getScorePicker = (
    gameNum,
    label,
    stateScoreKey,
    selectedValue,
    maxPoints
  ) => {
    var options = [{ placeholder: true }].concat(
      Array.apply(null, { length: maxPoints })
    )

    return (
      <Item picker>
        <Picker
          note={false}
          mode='dropdown'
          iosIcon={<Icon name='ios-arrow-down' />}
          style={{ flex: 1 }}
          textStyle={{ fontWeight: 'normal' }}
          selectedValue={selectedValue}
          placeholder={label}
          onValueChange={(val, idx) => {
            this.handleScoreChange(gameNum, stateScoreKey, val)
          }}
        >
          {options.map((element, index) => {
            if (element != null) {
              return <Picker.Item key={0} label={label} value='' />
            }
            return (
              <Picker.Item
                key={index}
                label={index.toString()}
                value={index - 1}
              />
            )
          })}
        </Picker>
      </Item>
    )
  }

  render() {
    const { matchNum, league, curTeamId, validate } = this.props
    const {
      oppTeamId,
      results,
      spiritScore,
      comment
    } = this.props.matchSubmission

    var resultOptions = [{ placeholder: true }].concat(
      Object.values(Enums.matchResult)
    )

    return (
      <Card>
        <CardItem>
          <Body>
            <Badge>
              <Text>Match {matchNum + 1}</Text>
            </Badge>

            <TeamPicker
              label='Opponent'
              loading={false}
              teams={league.teams != null ? league.teams : []}
              curTeamId={oppTeamId}
              excludeTeamId={curTeamId}
              onTeamUpdated={val => this.handleChange('oppTeamId', val)}
              validate={validate}
            />

            {Array.apply(
              null,
              new Array(parseInt(league.numGamesPerMatch, 10))
            ).map((e, gameIndex) => {
              var gameString =
                'Result ' + (league.numGamesPerMatch > 1 ? gameIndex + 1 : '')
              var gameKey = 'oppTeamId' + gameIndex
              var obj = []
              obj.push(
                <ResultPicker
                  key={gameKey}
                  gameIndex={gameIndex}
                  resultOptions={resultOptions}
                  result={results[gameIndex]}
                  gameString={gameString}
                  validate={validate}
                  league={league}
                  handleScoreChange={this.handleScoreChange}
                />
              )

              if (league.isAskForScores) {
                obj.push(
                  <Grid key={'scores' + gameIndex}>
                    <Col>
                      {this.getScorePicker(
                        gameIndex,
                        'We Got',
                        'scoreUs',
                        results[gameIndex].scoreUs,
                        parseInt(league.maxPointsPerGame, 10)
                      )}
                    </Col>
                    <Col>
                      {this.getScorePicker(
                        gameIndex,
                        'They Got',
                        'scoreThem',
                        results[gameIndex].scoreThem,
                        parseInt(league.maxPointsPerGame, 10)
                      )}
                    </Col>
                  </Grid>
                )
              }

              return obj
            })}

            <SpiritPicker
              spiritScore={spiritScore}
              validate={validate}
              onValueChange={(val, index) => {
                this.handleChange('spiritScore', val)
              }}
            />

            <Item
              regular
              style={{ marginTop: 10 }}
              error={
                spiritScore != '' &&
                parseFloat(spiritScore) < 4 &&
                comment.length < 3
              }
            >
              <Textarea
                style={{ flex: 1, paddingTop: 5, paddingBottom: 5 }}
                rowSpan={3}
                placeholder='Comments'
                placeHolderTextStyle={{ color: '#d3d3d3' }}
                onChangeText={v => this.handleChange('comment', v)}
                value={comment}
              />
            </Item>

            {spiritScore != '' &&
              parseFloat(spiritScore) < 4 &&
              comment.length < 4 && (
                <Item style={{ marginTop: 10, borderBottomWidth: 0 }}>
                  <Messages
                    type='error'
                    message='A comment is required when a spirit score of 3.5 or less is given'
                  />
                </Item>
              )}
          </Body>
        </CardItem>
      </Card>
    )
  }
}

export default ScoreReporterMatch
