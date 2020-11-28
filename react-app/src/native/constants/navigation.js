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
      height: 52
    },
    headerTintColor: Colors.inverseTextColor,
    headerTitleStyle: {
      fontSize: 14
    },
    backButtonTintColor: Colors.inverseTextColor
  },
  mainTitle: {
    header: ({ scene, previous, navigation }) => {      
      return (
        <View
          style={{
            flex: 1,
            backgroundColor: Colors.brandPrimary,
            flexDirection: 'row',
            alignItems: 'center',
            justifyContent: 'center',
            height:52
          }}
        >
          <Image
            style={{ width: 170, height: 52, margin: 'auto' }}
            source={require('../../images/header-logo.png')}
          />
          {previous && (
            <View style={{ width: 54 }}>
              <Text />
            </View>
          )}
        </View>
      )
    }
  },
  tabProps: {
    swipeEnabled: false,
    activeBackgroundColor: 'rgba(255,255,255,0.1)',
    inactiveBackgroundColor: Colors.brandPrimary,
    tabBarStyle: { backgroundColor: Colors.brandPrimary }
  },

  icons: {
    style: { color: 'white', height: 30, width: 30 }
  },

  tabConfig: {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: 'gray',
      style: {
        backgroundColor: '#303030'
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
        fontSize: 10,
        fontWeight: '600',
        letterSpacing: 1,
        margin: 0
      }
    }
  },
  bottomTabConfig: {
    tabBarOptions: {
      activeTintColor: 'white',
      inactiveTintColor: 'gray',
      activeBackgroundColor: Colors.brandDark,
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
