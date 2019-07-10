import React from 'react'
import Colors from '../../../native-base-theme/variables/commonColor'
import { View, Text, Image } from 'react-native'

export default {
  navbarProps: {
    navigationBarStyle: {
      backgroundColor: Colors.brandPrimary,
      shadowOpacity: 0,
      shadowOffset: {
        height: 0,
      },
      elevation: 0,
      height: 52
    },
    backButtonTintColor: Colors.inverseTextColor,
  },
  screenProps: {
    renderTitle: (props) => {
      return (
        <View style={{ flex: 1, backgroundColor: 'transparent', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
          <Image style={{ width: 170, height: 52, margin: 'auto' }} source={require('../../images/header-logo.png')} />
          {props.back &&
            <View style={{ width: 54 }}>
              <Text />
            </View>
          }
        </View>
      )
    }
  },
  tabProps: {
    swipeEnabled: false,
    activeBackgroundColor: 'rgba(255,255,255,0.1)',
    inactiveBackgroundColor: Colors.brandPrimary,
    tabBarStyle: { backgroundColor: Colors.brandPrimary },
  },

  icons: {
    style: { color: 'white', height: 30, width: 30 },
  },
};
