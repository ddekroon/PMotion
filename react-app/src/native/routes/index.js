import React from 'react';
import { Scene, Tabs, Stack } from 'react-native-router-flux';
import { View, Text, Image } from 'react-native';

import DefaultProps from '../constants/navigation';

import SignUpContainer from '../../containers/SignUp';
import SignUpComponent from '../components/pages/SignUp';

import LoginContainer from '../../containers/Login';
import LoginComponent from '../components/pages/Login';

import ForgotPasswordContainer from '../../containers/ForgotPassword';
import ForgotPasswordComponent from '../components/pages/ForgotPassword';

import UpdateProfileContainer from '../../containers/UpdateProfile';
import UpdateProfileComponent from '../components/pages/UpdateProfile';

import MemberContainer from '../../containers/Member';
import ProfileComponent from '../components/pages/Profile';

import LeaguesContainer from '../../containers/Leagues';
import LeaguesComponent from '../components/pages/Leagues';
import LeaguePage from '../components/leagues/League';

import ScoreReporterContainer from '../../containers/ScoreReporter';
import ScoreReporterComponent from '../components/pages/ScoreReporter';

const Index = (
  <Stack hideNavBar>
    <Scene hideNavBar>
      <Tabs
        key="tabbar"
        swipeEnabled
        type="replace"
        showLabel={false}
        {...DefaultProps.tabProps}
      >
        <Stack
          key="leagues"
          title='Leagues'
          icon={() =>
            <View style={{ flex: 1, flexDirection: 'column', alignItems: 'center', alignSelf: 'center', justifyContent: 'center' }}>
              <Image style={{ width: 20, height: 20 }} source={require('../../images/icons/leagues.png')} />
              <Text style={{ marginTop: 2, color: '#ffffff', fontSize: 11 }}>Leagues</Text>
            </View>
          }
          {...DefaultProps.navbarProps}
        >
          <Scene
            key="leagues"
            component={LeaguesContainer}
            Layout={LeaguesComponent}
            {...DefaultProps.screenProps}
          />
        </Stack>

        <Stack
          key="reportScores"
          title="Report Scores"
          icon={() =>
            <View style={{ flex: 1, flexDirection: 'column', alignItems: 'center', alignSelf: 'center', justifyContent: 'center' }}>
              <Image style={{ height: 20, width: 27 }} source={require('../../images/icons/scores.png')} />
              <Text style={{ marginTop: 2, color: '#ffffff', fontSize: 11 }}>Report Scores</Text>
            </View>
          }
          {...DefaultProps.navbarProps}
        >
          <Scene
            key="reportScores"
            component={ScoreReporterContainer}
            Layout={ScoreReporterComponent}
            {...DefaultProps.screenProps}
          />
        </Stack>

        <Stack
          key="registration"
          title="Registration"
          icon={() =>
            <View style={{ flex: 1, flexDirection: 'column', alignItems: 'center', alignSelf: 'center', justifyContent: 'center' }}>
              <Image style={{ width: 20, height: 20 }} source={require('../../images/icons/registration.png')} />
              <Text style={{ marginTop: 2, color: '#ffffff', fontSize: 11 }}>Registration</Text>
            </View>
          }
          {...DefaultProps.navbarProps}
        >
          <Scene
            key="registration"
            component={MemberContainer}
            Layout={ProfileComponent}
            {...DefaultProps.screenProps}
          />
          <Scene
            back
            key="signUp"
            title="SIGN UP"
            {...DefaultProps.navbarProps}
            component={SignUpContainer}
            Layout={SignUpComponent}
            {...DefaultProps.screenProps}
          />
          <Scene
            back
            key="login"
            title="LOGIN"
            {...DefaultProps.navbarProps}
            component={LoginContainer}
            Layout={LoginComponent}
            {...DefaultProps.screenProps}
          />
          <Scene
            back
            key="forgotPassword"
            title="FORGOT PASSWORD"
            {...DefaultProps.navbarProps}
            component={ForgotPasswordContainer}
            Layout={ForgotPasswordComponent}
            {...DefaultProps.screenProps}
          />
          <Scene
            back
            key="updateProfile"
            title="UPDATE PROFILE"
            {...DefaultProps.navbarProps}
            component={UpdateProfileContainer}
            Layout={UpdateProfileComponent}
            {...DefaultProps.screenProps}
          />
        </Stack>
      </Tabs>
    </Scene>

    <Scene
      back
      clone
      key="league"
      title=""
      {...DefaultProps.navbarProps}
      component={LeaguesContainer}
      Layout={LeaguePage}
    />

  </Stack>
);

export default Index;
