import React from 'react';
import PropTypes from 'prop-types';
import {
  Container, Content, Text, Form, Item, Label, Input, Button, Picker, Icon
} from 'native-base';
import { Actions } from 'react-native-router-flux';
import Loading from './Loading';
import Messages from './Messages';
import Header from './Header';
import Spacer from './Spacer';

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
      sportId: '',
      leagueId: ''
    };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange = (name, val) => {
    this.setState({
      [name]: val,
    });
  }

  onValueChange(value) {
    this.setState({
      sportId: value
    });
  }

  handleSubmit = () => {
    const { onFormSubmit } = this.props;
    onFormSubmit(this.state)
      .then(() => Actions.login())
      .catch(e => console.log(`Error: ${e}`));
  }

  render() {
    const { loading, error } = this.props;

    if (loading) return <Loading />;

    return (
      <Container>
        <Content padder>
          {error && <Messages message={error} />}

          <Form>
            <Item picker>
              <Picker
                note={false}
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={{ flex: 1 }}
                selectedValue={this.state.sportId}
                placeholder="Sport"
                onValueChange={this.onValueChange.bind(this)}
              >
                {
                  this.props.sports.map((curSport) => {
                    return <Picker.Item key={curSport.id} label={curSport.name} value={curSport.id} />
                  })
                }
              </Picker>
            </Item>

            <Item fixedLabel>
              <Picker
                note={false}
                mode="dropdown"
                iosIcon={<Icon name="arrow-down" />}
                style={{ flex: 1 }}
                selectedValue={this.state.leagueId}
                placeholder="Sport"
                onValueChange={this.onValueChange.bind(this)}
              >
                {
                  this.props.sports.map((curSport) => {
                    return <Picker.Item key={curSport.id} label={curSport.name} value={curSport.id} />
                  })
                }
              </Picker>
            </Item>

            <Item fixedLabel>
              <Label>
                Team
              </Label>
              <Input
                autoCapitalize="none"
                keyboardType="email-address"
                onChangeText={v => this.handleChange('email', v)}
              />
            </Item>

            <Item fixedLabel>
              <Label>
                Password
              </Label>
              <Input secureTextEntry onChangeText={v => this.handleChange('password', v)} />
            </Item>

            <Item fixedLabel>
              <Label>
                Confirm Password
              </Label>
              <Input secureTextEntry onChangeText={v => this.handleChange('password2', v)} />
            </Item>

            <Spacer size={20} />

            <Button block onPress={this.handleSubmit}>
              <Text>
                Sign Up
              </Text>
            </Button>
          </Form>
        </Content>
      </Container >
    );
  }
}

export default ScoreReporter;
