import React from 'react'
import { Container, Content, Text, Icon, View } from 'native-base'
import { StyleSheet } from 'react-native'

import CommonColors from '../../../native-base-theme/variables/commonColor'

export default class Registration extends React.Component {
  render() {
    return (
      <Container>
        <Content>
          <View style={Styles.container}>
            <View style={Styles.iconContainer}>
              <Icon name="construct" style={Styles.icon} />
            </View>
            <Text style={Styles.text}>Coming Soon</Text>
          </View>
        </Content>
      </Container>
    )
  }
}

const Styles = StyleSheet.create({
  container: {
    flex: 1,
    marginTop: 50,
    alignItems: 'center'
  },
  iconContainer: {
    backgroundColor: '#fff',
    flex: 1,
    width: 160,
    height: 160,
    borderRadius: 80,
    overflow: 'hidden',
    marginBottom: 20,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.15,
    shadowRadius: 80,
    elevation: 3
  },
  icon: {
    fontSize: 96,
    color: CommonColors.brandPrimary,
    marginTop: 30
  },
  text: {
    fontSize: 36,
    color: CommonColors.brandPrimary
  }
})
