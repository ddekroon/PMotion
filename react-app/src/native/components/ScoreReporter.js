import React from 'react';
import PropTypes from 'prop-types';
import {
  Container, Content, Text, Form, Item, Label, Input, Button, Picker, Icon, Card, CardItem, Body
} from 'native-base';
import Loading from './Loading';
import Messages from './Messages';
import Spacer from './Spacer';
import ScoreReporterMatch from './ScoreReporterMatch';
import DateTimeHelpers from '../../utils/datetimehelpers';
import LeagueHelpers from '../../utils/leaguehelpers';

class ScoreReporter extends React.Component {
  static propTypes = {
    error: PropTypes.string,
    loading: PropTypes.bool.isRequired,
    onFormSubmit: PropTypes.func.isRequired,
    sports: PropTypes.array.isRequired,
    seasons: PropTypes.array.isRequired
  }

  static defaultProps = {
    error: null,
  }

  constructor(props) {
    super(props);

    this.state = {
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      password2: '',
      sportId: '1',
      leagueId: '1640',
      matches: []
    };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.updateMatchHandler = this.updateMatchHandler.bind(this);
  }

  calculateIsLeaguesToSelect = () => {
    if (this.props.seasons == null || this.state.sportId == '') {
      return false;
    }

    var numLeagues = 0;
    var seasonsWithLeagues = this.props.seasons[this.state.sportId].forEach((curSeason) => {
      if (curSeason.leagues == null || numLeagues > 0) {
        return true; //continue
      }

      numLeagues += curSeason.leagues.length;
    });

    return numLeagues > 0;
  }

  handleChange = (name, val) => {
    this.setState({
      [name]: val,
    });
  }

  handleSubmit = () => {
    const { onFormSubmit } = this.props;
    onFormSubmit(this.state)
      .then(() => console.log("Form submitted"))
      .catch(e => console.log(`Error: ${e}`));
  }

  updateMatchHandler = (matchNum, obj) => {
    var matches = this.state.matches;
    matches[matchNum] = obj;

    this.setState({
      matches: matches
    })
  }

  render() {
    const { loading, error } = this.props;

    if (loading) return <Loading />;

    var leaguePicker;
    var isLeagues = this.calculateIsLeaguesToSelect();
    var curLeague = this.state.sportId != '' ? LeagueHelpers.getLeagueFromSeasons(this.props.seasons[this.state.sportId], this.state.leagueId) : null;
    var isMultipleSeasons = this.state.sportId != '' ? this.props.seasons[this.state.sportId].length > 1 : false;

    if (this.state.sportId != '' && !isLeagues) {
      leaguePicker = <Item><Content padder><Text style={{ fontStyle: italic }}>No leagues to select</Text></Content></Item>;
    } else if (isLeagues) {
      leaguePicker = <Item picker>
        <Picker
          note={false}
          mode="dropdown"
          iosIcon={<Icon name="arrow-down" />}
          style={{ flex: 1 }}
          selectedValue={this.state.leagueId}
          placeholder="League"
          onValueChange={(val, index) => this.handleChange('leagueId', val)}
        >
          <Picker.Item key={0} label={'League'} value={''} />
          {
            this.props.seasons[this.state.sportId].map((curSeason) => {
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
                      selectedValue={this.state.sportId}
                      placeholder="Sport"
                      onValueChange={(val, index) => {
                        this.handleChange('sportId', val)
                        this.handleChange('leagueId', '')
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
                </Body>
              </CardItem>
            </Card>

            {
              curLeague != null &&
              Array.apply(null, new Array(parseInt(curLeague.numMatches, 10))).map((e, index) => {
                return <ScoreReporterMatch
                  league={curLeague}
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
                    <Input onChangeText={v => this.handleChange('name', v)} />
                  </Item>
                  <Item inlineLabel underline>
                    <Label>
                      Email
                    </Label>
                    <Input
                      autoCapitalize="none"
                      keyboardType="email-address"
                      onChangeText={v => this.handleChange('email', v)}
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
