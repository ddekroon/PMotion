import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { KeyboardAvoidingView, Image } from 'react-native'
import {
  Content,
  Text,
  Form,
  Item,
  Label,
  Input,
  Button,
  Picker,
  Icon,
  Card,
  CardItem,
  Body,
  Header
} from 'native-base'

import Loading from '../components/common/Loading'
import Spacer from '../components/common/Spacer'
import TeamPicker from '../components/common/TeamPicker'
import ScoreReporterMatch from '../components/scorereporter/Match'
import DateTimeHelpers from '../../utils/datetimehelpers'
import ValidationHelpers from '../../utils/validationhelpers'
import ToastHelpers from '../../utils/toasthelpers'

import Enums from '../../constants/enums'
import {
  submitScoreSubmission,
  updateScoreSubmission,
  resetMatches,
  resetSubmission
} from '../../actions/scoreSubmission'
import { fetchLeague } from '../../actions/leagues' //Gets the leagues from the web.


class ScoreReporter extends React.Component {

  static propTypes = {  //These are actions
    error: PropTypes.string,
    isLoading: PropTypes.bool.isRequired,
    getLeague: PropTypes.func.isRequired, //getLeague is a function!
    onFormSubmit: PropTypes.func.isRequired,
    leagues: PropTypes.object.isRequired,
    sports: PropTypes.array.isRequired,
    seasons: PropTypes.object.isRequired, //seasons is a object (JSON string)
    scoreSubmission: PropTypes.object.isRequired,
    updateScoreSubmission: PropTypes.func.isRequired,
    resetMatches: PropTypes.func.isRequired,
    resetSubmission: PropTypes.func.isRequired
  }

  static defaultProps = {
    error: null
  }

  constructor(props) {
    super(props)

    this.handleChange = this.handleChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
    this.updateMatchHandler = this.updateMatchHandler.bind(this)
  }

  calculateIsLeaguesToSelect = () => {
    const { seasons, scoreSubmission } = this.props

    if (
      seasons == null ||
      Object.keys(seasons).length === 0 ||
      scoreSubmission.sportId == ''
    ) {
      return false
    }

    var numLeagues = 0
    seasons[scoreSubmission.sportId]
      .filter(curSeason => curSeason.leagues != null)
      .forEach(curSeason => {
        numLeagues += curSeason.leagues.length
      })

    return numLeagues > 0
  }

  handleSubmit = () => {
    const { onFormSubmit } = this.props
    onFormSubmit().catch(e => {
      ToastHelpers.showToast(Enums.messageTypes.Error, e.message)
      console.log(`Error: ${e.message}`)
    })
  }

  handleChange = (name, val) => {
    const { scoreSubmission } = this.props

    var newSubmission = {
      ...scoreSubmission,
      [name]: val
    }

    if (name == 'sportId') {
      newSubmission['leagueId'] = ''
      newSubmission['teamId'] = ''
    }

    // Get the league via the API and store it into the store under store.leagues
    if (name == 'leagueId') {
      newSubmission['teamId'] = ''
      this.props.getLeague(val)
    }

    if (name == 'teamId' && val != '') {
      const { leagues } = this.props
      var league = leagues[newSubmission.leagueId]

      if (
        league.scheduledMatches == null ||
        league.scheduledMatches.length == 0
      ) {
        return
      }

      if (
        league.dateInScoreReporter != null &&
        league.dateInScoreReporter.id != null
      ) {
        var teamMatches = league.scheduledMatches.filter(curMatch => {
          return (
            curMatch.dateId == league.dateInScoreReporter.id &&
            (curMatch.teamOneId == val || curMatch.teamTwoId == val)
          )
        })

        for (
          var i = 0;
          i < Math.min(parseInt(league.numMatches, 10), teamMatches.length);
          i++
        ) {
          if (teamMatches[i].teamOneId == val) {
            newSubmission.matches[i].oppTeamId = teamMatches[i].teamTwoId
          } else {
            newSubmission.matches[i].oppTeamId = teamMatches[i].teamOneId
          }
        }
      }
    }

    this.props.updateScoreSubmission(newSubmission)

    // Pending sport, league, or team have been udpated the matches need to be reset
    // Needs to happen after first update score submission or else the matches udpate will get lost with the overall submission update
    if (name == 'sportId' || name == 'leagueId' || name == 'teamId') {
      this.props.resetMatches()
    }
  }

  updateMatchHandler = (matchNum, obj) => {
    var { scoreSubmission } = this.props

    var newMatchResults = scoreSubmission.matches.map((match, index) => {
      if (index != matchNum) {
        return match
      }

      return obj
    })

    var newSubmissions = {
      ...scoreSubmission,
      matches: newMatchResults
    }

    this.props.updateScoreSubmission(newSubmissions)
  }

  render() {
    console.log("ScoreReporter.js")

    const { //Data
      loading,
      error,
      scoreSubmission,
      seasons,
      leagues,
      resetSubmission
    } = this.props

    if (loading) return <Loading />

    var leaguePicker
    var isLeagues = this.calculateIsLeaguesToSelect()
    var league = leagues[scoreSubmission.leagueId]
    var isMultipleSeasons =
      scoreSubmission.sportId != ''
        ? seasons[scoreSubmission.sportId].length > 1
        : false

    if (scoreSubmission.sportId != '' && !isLeagues) {
      leaguePicker = (
        <Item>
          <Content padder>
            <Text style={{ fontStyle: 'italic' }}>No leagues to select</Text>
          </Content>
        </Item>
      )
    } else if (isLeagues) {
      leaguePicker = (
        <Item picker error={scoreSubmission.leagueId == ''}>
          <Picker
            placeholder="League"
            note={false}
            mode="dropdown"
            iosIcon={<Icon name="arrow-down" />}
            
            style={{ flex: 1 }}
            selectedValue={scoreSubmission.leagueId}
            onValueChange={(val, index) => {
              this.handleChange('leagueId', val)
            }} 
          >
            <Picker.Item key={0} label={'League'} value={''} />

            {/* always at spot 0 here*/}
            
          </Picker>
        </Item>
      )
    }

    return (
      <KeyboardAvoidingView style={{ flex: 1 }} behavior="padding" enabled>
        <Content padder>
          {!scoreSubmission.submitted && (
            <Form>
              <Card>
                <CardItem>
                  <Body>
                    <Item picker error={scoreSubmission.sportId == ''}>
                      <Picker
                        note={false}
                        mode="dropdown"
                        iosIcon={<Icon name="arrow-down" />}
                        style={{ flex: 1 }}
                        selectedValue={scoreSubmission.sportId}
                        placeholder="Sport"
                        onValueChange={(val, index) => {
                          this.handleChange('sportId', val)
                        }}
                      >
                        <Picker.Item key={0} label="Sport" value="" />
                        {this.props.sports.map(curSport => {
                          return (
                            <Picker.Item
                              key={curSport.id}
                              label={curSport.name}
                              value={curSport.id}
                            />
                          )
                        })}
                      </Picker>
                    </Item>

                    {leaguePicker}

                    {league != null && (
                      <TeamPicker
                        loading={league.isFetching}
                        teams={league.teams != null ? league.teams : []}
                        curTeamId={scoreSubmission.teamId}
                        onTeamUpdated={val => this.handleChange('teamId', val)}
                      />
                    )}
                  </Body>
                </CardItem>
              </Card>

              {league != null &&
                !league.isFetching &&
                scoreSubmission.teamId != '' &&
                Array.apply(
                  null,
                  new Array(parseInt(league.numMatches, 10))
                ).map((e, index) => {
                  return (
                    <ScoreReporterMatch
                      key={index}
                      curTeamId={scoreSubmission.teamId}
                      league={league}
                      matchNum={index}
                      updateMatchHandler={this.updateMatchHandler}
                      matchSubmission={scoreSubmission.matches[index]}
                    />
                  )
                })}

              <Card>
                <CardItem>
                  <Body>
                    <Item
                      inlineLabel
                      underline
                      error={
                        scoreSubmission.teamId != '' &&
                        scoreSubmission.name.length < 3
                      }
                    >
                      <Label>Name</Label>
                      <Input
                        onChangeText={v => this.handleChange('name', v)}
                        value={scoreSubmission.name}
                      />
                    </Item>
                    <Item
                      inlineLabel
                      underline
                      error={
                        scoreSubmission.teamId != '' &&
                        !ValidationHelpers.isValidEmail(scoreSubmission.email)
                      }
                    >
                      <Label>Email</Label>
                      <Input
                        autoCapitalize="none"
                        keyboardType="email-address"
                        onChangeText={v => this.handleChange('email', v)}
                        value={scoreSubmission.email}
                      />
                    </Item>

                    <Spacer size={20} />

                    <Button
                      block
                      onPress={this.handleSubmit}
                      disabled={scoreSubmission.submitting}
                    >
                      <Text>
                        {scoreSubmission.submitting
                          ? 'Submitting...'
                          : 'Submit Score'}
                      </Text>
                    </Button>
                  </Body>
                </CardItem>
              </Card>
            </Form>
          )}

          {scoreSubmission.submitted && (
            <Card>
              <CardItem>
                <Body>
                  <Text>Your score submission has been received.</Text>
                  <Spacer size={20} />
                  <Button block onPress={() => resetSubmission()}>
                    <Text>Submit another score</Text>
                  </Button>
                </Body>
              </CardItem>
            </Card>
          )}
        </Content>
      </KeyboardAvoidingView>
    )
  }
}

const mapStateToProps = state => ({
  leagues: state.leagues || {},
  isLoading: state.status.loading || false,
  sports: state.lookups.sports || [],
  seasons: state.lookups.scoreReporterSeasons || [],
  scoreSubmission: state.scoreSubmission || {}
})

const mapDispatchToProps = {
  updateScoreSubmission: updateScoreSubmission,
  onFormSubmit: submitScoreSubmission,
  getLeague: fetchLeague,
  resetMatches: resetMatches,
  resetSubmission: resetSubmission
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ScoreReporter)
