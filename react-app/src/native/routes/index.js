import React from 'react';
import { Scene, Tabs, Stack } from 'react-native-router-flux';
import { Icon } from 'native-base';
import { View, Text, Image } from 'react-native';

import DefaultProps from '../constants/navigation';
import AppConfig from '../../constants/config';

import RecipesContainer from '../../containers/Recipes';
import RecipesComponent from '../components/Recipes';
import RecipeViewComponent from '../components/Recipe';

import SignUpContainer from '../../containers/SignUp';
import SignUpComponent from '../components/SignUp';

import LoginContainer from '../../containers/Login';
import LoginComponent from '../components/Login';

import ForgotPasswordContainer from '../../containers/ForgotPassword';
import ForgotPasswordComponent from '../components/ForgotPassword';

import LocaleContainer from '../../containers/Locale';
import LocaleComponent from '../components/Locale';

import UpdateProfileContainer from '../../containers/UpdateProfile';
import UpdateProfileComponent from '../components/UpdateProfile';

import MemberContainer from '../../containers/Member';
import ProfileComponent from '../components/Profile';

import LeaguesContainer from '../../containers/Leagues';
import LeaguesComponent from '../components/Leagues';

import ScoreReporterContainer from '../../containers/ScoreReporter';
import ScoreReporterComponent from '../components/ScoreReporter';

import LeaguePage from '../components/LeaguePage';

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
            key="locale"
            title="CHANGE LANGUAGE"
            {...DefaultProps.navbarProps}
            component={LocaleContainer}
            Layout={LocaleComponent}
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

        <Stack
          key="recipes"
          title="RECIPES"
          icon={() => <Icon name="book" {...DefaultProps.icons} />}
          {...DefaultProps.navbarProps}
        >
          <Scene
            key="recipes"
            component={RecipesContainer}
            Layout={RecipesComponent}
            {...DefaultProps.screenProps}
          />
        </Stack>
      </Tabs>
    </Scene>

    <Scene
      back
      clone
      key="recipe"
      title="RECIPE"
      {...DefaultProps.navbarProps}
      component={RecipesContainer}
      Layout={RecipeViewComponent}
      {...DefaultProps.screenProps}
    />
    
    <Scene
        back
        key="league" 
        component={LeaguePage} 
        {...DefaultProps.navbarProps}
        {...DefaultProps.screenProps}
    />

  </Stack>
);

export default Index;
