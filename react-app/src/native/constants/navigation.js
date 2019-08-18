import React from 'react'
import Colors from '../../../native-base-theme/variables/commonColor'
import { View, Text, Image } from 'react-native'

export default {
  navbarProps: {
    headerStyle: {
      backgroundColor: Colors.brandPrimary,
      shadowOpacity: 0,
      shadowOffset: {
        height: 0
      },
      elevation: 0,
      height: 52,
      borderBottomWidth: 0
    },
    headerTintColor: Colors.inverseTextColor,
    headerTitleStyle: {
      fontWeight: 'bold'
    },
    backButtonTintColor: Colors.inverseTextColor
  },
  mainTitle: {
    headerTitle: props => {
      return (
        <View
          style={{
            flex: 1,
            backgroundColor: 'transparent',
            flexDirection: 'row',
            alignItems: 'center',
            justifyContent: 'center'
          }}
        >
          <Image
            style={{ width: 170, height: 52, margin: 'auto' }}
            source={require('../../images/header-logo.png')}
          />
          {props.back && (
            <View style={{ width: 54 }}>
              <Text />
            </View>
          )}
        </View>
      )
    }
  },

  icons: {
    style: { color: 'white', height: 30, width: 30 }
  },

  tabConfig: {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: Colors.brandGray,
      style: {
        backgroundColor: Colors.brandPrimary
      },
      tabStyle: {
        paddingTop: 12,
        paddingBottom: 12,
        paddingLeft: 2,
        paddingRight: 2
      },
      indicatorStyle: {
        borderBottomColor: 'red',
        borderBottomWidth: 3
      },
      labelStyle: {
        fontSize: 11,
        fontWeight: '600',
        letterSpacing: 0.5,
        margin: 0
      }
    }
  },
  bottomTabConfig: {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: Colors.brandGray,
      style: {
        backgroundColor: Colors.brandPrimary
      },
      tabStyle: {
        paddingTop: 2,
        paddingBottom: 2,
        paddingLeft: 2,
        paddingRight: 2
      },
      labelStyle: {}
    }
  }
}
