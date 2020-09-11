import React from 'react';
import { Container, Content, Card, Form, Input, Label, Button, Text, Item } from 'native-base';
import Header from '../components/common/Header';
import ValidationHelpers from '../../utils/validationhelpers';
import ToastHelpers from '../../utils/toasthelpers';
import { submitForgotPassword } from '../../actions/forgotPassword';
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { setMessageToRead, addMessage } from '../../actions/toastMessage';


class ForgotPassword extends React.Component {

    static propTypes = {
        onFormSubmit: PropTypes.func.isRequired,
        addMessage: PropTypes.func.isRequired,
        setMessageToRead: PropTypes.func.isRequired,
    }

    constructor(props) {
        super(props)
        this.state = {
            email:'',
        }
    }

    submit() {
        if ( ValidationHelpers.isValidEmail(this.state.email)) {
            let obj = new Object;

            obj.userEmail = this.state.email
            obj.action = 'submit'

            const { onFormSubmit } = this.props
            onFormSubmit(obj).catch(e => {
                ToastHelpers.showToast(Enums.messageTypes.Error, e.message)
            })

            this.props.addMessage('Success, please check your email for a link to reset your password')
            this.props.navigation.goBack();          

        } else {
            ToastHelpers.showToast(null, "Invalid Email");
        }
    }

    render() {
        const { navigation } = this.props;

        return (
            <Container>
                <Content>
                    <Card style={{paddingLeft:10}}>
                        <Header
                            title='Reset an Account Password'
                            content="Enter your email and we'll have you on your way in a jiffy!"
                        />

                        <Form>
                            <Item>
                                <Label>Email:</Label>
                                <Input
                                    keyboardType={'email-address'}
                                    autoComplete={'email'}
                                    autoCapitalization='none'
                                    value={this.state.email}
                                    onChangeText={(email) => this.setState({email:email})}
                                />
                            </Item>

                            <Button block
                                title={'Submit'}
                                onPress={ () => {
                                    this.submit()
                                }}
                            >
                                <Text>Submit</Text>
                            </Button>
                        </Form>
                    </Card>
                </Content>
            </Container>
        )
    }
} 


const mapStateToProps = state => ({
    theState: state
  })
  
  const mapDispatchToProps = {
    onFormSubmit: submitForgotPassword,
    setMessageToRead: setMessageToRead,
    addMessage: addMessage,
  }
  
  export default connect(
    mapStateToProps,
    mapDispatchToProps
  )(ForgotPassword)