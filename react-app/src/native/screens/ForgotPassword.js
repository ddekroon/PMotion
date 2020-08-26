import React from 'react';
import { Container, Content, Card, Form, Input, Label, Button, Text, Item } from 'native-base';
import { View } from 'react-native';
import Header from '../components/common/Header';
import ValidationHelpers from '../../utils/validationhelpers';
import ToastHelpers from '../../utils/toasthelpers';


export default class ForgotPassword extends React.Component {

    constructor(props) {
        super(props)
        this.state = {
            email:'',
        }
    }

    submit() {
        if ( ValidationHelpers.isValidEmail(this.state.email)) {
            //Send email
            console.log("Submitted")
            return true;
         
        } else {
            ToastHelpers.showToast(null, "Invalid Email");
            return false;
        }
    }

    render() {
        const { navigation } = this.props;

        return (
            <Container>
                <Content>
                    <Card>
                        <Header
                            title='Reset an Account Password'
                            content="Enter your email and we'll have you on your way in a jiffy!"
                        />

                        <Form>
                            <Item>
                                <Label>Email:</Label>
                                <Input
                                    autoCapitalization='none'
                                    value={this.state.email}
                                    onChangeText={(email) => this.setState({email:email})}
                                />
                            </Item>

                            <Button block
                                title={'Submit'}
                                onPress={ () => {
                                    if (this.submit()) {
                                        navigation.navigate('Registration', {showToast:true, toastString:'Successfully Sent Email.'})
                                    }
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