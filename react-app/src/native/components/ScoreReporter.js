import React from 'react';
import PropTypes from 'prop-types';
import {
  Container, Content, Text, Form, Item, Label, Input, Button, Picker, Icon, Card, CardItem, Body
} from 'native-base';
import Loading from './Loading';
import Messages from './Messages';
import Spacer from './Spacer';
import TeamPicker from './common/TeamPicker';
import ScoreReporterMatch from './ScoreReporterMatch';
import DateTimeHelpers from '../../utils/datetimehelpers';
import LeagueHelpers from '../../utils/leaguehelpers';

class ScoreReporter extends React.Component {
  static propTypes = {
    error: PropTypes.string,
    loading: PropTypes.bool.isRequired,
    getLeague: PropTypes.func.isRequired,
    onFormSubmit: PropTypes.func.isRequired,
    leagues: PropTypes.object.isRequired,
    sports: PropTypes.array.isRequired,
    seasons: PropTypes.object.isRequired,
    scoreSubmission: PropTypes.object.isRequired,
    updateScoreSubmission: PropTypes.func.isRequired
  }

  static defaultProps = {
    error: null,
  }

  constructor(props) {
    super(props);

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.updateMatchHandler = this.updateMatchHandler.bind(this);
  }

  calculateIsLeaguesToSelect = () => {
    const { seasons, scoreSubmission } = this.props;

    if (seasons == null || Object.keys(seasons).length === 0 || scoreSubmission.sportId == '') {
      return false;
    }

    var numLeagues = 0;
    seasons[scoreSubmission.sportId].filter((curSeason) => curSeason.leagues != null)
      .forEach((curSeason) => {
        numLeagues += curSeason.leagues.length;
      });

    return numLeagues > 0;
  }

  handleSubmit = () => {
    /*const { onFormSubmit } = this.props;
    onFormSubmit()
      .then(() => console.log("Form submitted"))
      .catch(e => console.log(`Error: ${e}`));*/
    console.log("handle submit");
  }

  handleChange = (name, val) => {
    var newSubmission = this.props.scoreSubmission;
    newSubmission[name] = val;

    this.props.updateScoreSubmission(newSubmission);

    // Get the league via the API and store it into the store under store.leagues
    if (name == 'leagueId') {
      this.props.getLeague(val);
    }
  }

  updateMatchHandler = (matchNum, obj) => {
    //var { scoreSubmission } = this.props;
    //scoreSubmission.matches[matchNum] = obj;

    console.log("Update Match: " + matchNum + " - " + obj);

    //this.props.updateScoreSubmission(scoreSubmission);
  }

  render() {
    const { loading, error, scoreSubmission, seasons, leagues } = this.props;

    if (loading) return <Loading />;

    var leaguePicker;
    var isLeagues = this.calculateIsLeaguesToSelect();
    var league = leagues[scoreSubmission.leagueId];
    var isMultipleSeasons = scoreSubmission.sportId != '' ? seasons[scoreSubmission.sportId].length > 1 : false;

    console.log("Score submission teamID: " + scoreSubmission.teamId);


    if (scoreSubmission.sportId != '' && !isLeagues) {
      leaguePicker = <Item><Content padder><Text style={{ fontStyle: italic }}>No leagues to select</Text></Content></Item>;
    } else if (isLeagues) {
      leaguePicker = <Item picker>
        <Picker
          note={false}
          mode="dropdown"
          iosIcon={<Icon name="arrow-down" />}
          style={{ flex: 1 }}
          selectedValue={scoreSubmission.leagueId}
          placeholder="League"
          onValueChange={(val, index) => {
            this.handleChange('leagueId', val)
            this.handleChange('teamId', '')
          }}
        >
          <Picker.Item key={0} label={'League'} value={''} />
          {
            seasons[scoreSubmission.sportId].map((curSeason) => {
              if (curSeason.leagues == null) {
                return;
              }

              return curSeason.leagues.map((curLeague) => {
                var leagueName = curLeague.name + " - " + DateTimeHelpers.getDayString(curLeague.dayNumber);
                if (isMultipleSeasons) {
                  leagueName = leagueName + " - " + curSeason.name;
                }

                return <Picker.Item key={curLeague.id} label={leagueName} value={curLeague.id} />
              });
            })
          }
        </Picker>
      </Item>
    }

    return (
      <Container>
        <Content padder>
          {error && <Messages message={error} />}

          <Form>
            <Card>
              <CardItem>
                <Body>
                  <Item picker>
                    <Picker
                      note={false}
                      mode="dropdown"
                      iosIcon={<Icon name="arrow-down" />}
                      style={{ flex: 1 }}
                      selectedValue={scoreSubmission.sportId}
                      placeholder="Sport"
                      onValueChange={(val, index) => {
                        this.handleChange('sportId', val)
                        this.handleChange('leagueId', '')
                        this.handleChange('teamId', '')
                      }}
                    >
                      <Picker.Item key={0} label='Sport' value='' />
                      {
                        this.props.sports.map((curSport) => {
                          return <Picker.Item key={curSport.id} label={curSport.name} value={curSport.id} />
                        })
                      }
                    </Picker>
                  </Item>

                  {leaguePicker}

                  {
                    league != null &&
                    <TeamPicker
                      loading={league.isFetching}
                      teams={league.teams != null ? league.teams : []}
                      curTeamId={scoreSubmission.teamId}
                      onTeamUpdated={(val) => this.handleChange('teamId', val)}
                    />
                  }
                </Body>
              </CardItem>
            </Card>

            {
              league != null && !league.isFetching && scoreSubmission.teamId != '' &&
              Array.apply(null, new Array(parseInt(league.numMatches, 10))).map((e, index) => {
                return <ScoreReporterMatch
                  key={index}
                  league={league}
                  matchNum={index}
                  updateMatchHandler={this.updateMatchHandler}
                  loading={loading} />
              })
            }

            <Card>
              <CardItem>
                <Body>
                  <Item inlineLabel underline>
                    <Label>
                      Name
                    </Label>
                    <Input
                      onChangeText={v => this.handleChange('name', v)}
                      value={scoreSubmission.name}
                    />
                  </Item>
                  <Item inlineLabel underline>
                    <Label>
                      Email
                    </Label>
                    <Input
                      autoCapitalize="none"
                      keyboardType="email-address"
                      onChangeText={v => this.handleChange('email', v)}
                      value={scoreSubmission.email}
                    />
                  </Item>

                  <Spacer size={20} />

                  <Button block onPress={this.handleSubmit}>
                    <Text>Submit Score</Text>
                  </Button>
                </Body>
              </CardItem>
            </Card>
          </Form>
        </Content>
      </Container >
    );
  }
}

export default ScoreReporter;
