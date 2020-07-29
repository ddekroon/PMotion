//this is a stub, not meant to be the final product
import React, {useState} from 'react';
import {
  Container, Content, Form, Item, Label, Input, Text, Button, View, Card,
} from 'native-base';
import Header from '../components/common/Header';
import Spacer from '../components/common/Spacer';

export default function Login({route, navigation}) {

    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')

    return (
      <Container>
        <Content>
            <Card style={{height:'100%'}}>
              <View style={{paddingBottom:10}}/>
                <Header
                  title="Welcome back"
                  content="Please use your email and password to login."
                />
                <Form>
                    <Item stackedLabel>
                    <Label>Email</Label>
                    <Input
                        autoCapitalize="none"
                        value={email}
                        keyboardType="email-address"
                        onChangeText={email => setEmail(email)}
                    />
                    </Item>

                    <Item stackedLabel>
                    <Label>Password</Label>
                    <Input
                        secureTextEntry
                        value={password}
                        onChangeText={pass => setPassword(pass)}
                    />
                </Item>

                <Spacer size={20} />

                <View padder>
                  <Button block title={'Login'} onPress={() => {
                    if (route.params.registerType == 'reregister') {
                      navigation.navigate("Previousleagues", {use:'reregister'})
                    } else {
                      navigation.navigate("PickSport", {registerType:route.params.registerType})
                    }
                  }}>
                    <Text>Login</Text>
                  </Button>
                </View>
              </Form>  
            </Card>
        </Content>
      </Container>            
    )
}
