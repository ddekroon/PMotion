'use strict';

import React, { Component } from 'react';
import { createStackNavigator } from 'react-navigation';
import Index from './index.js';
import FieldStatus from './fieldStatus.js';
import ScoreReporter from './scoreReport.js';
import Schedule from './schedule.js';
import SportMain from './sportMain.js';
import styles from './styles.js';

// The app may(?) need to request internet permissions on devices. I know how to do for android in Android Studio, but idk if it's needed for iOS too

const App = createStackNavigator({
    Home: { screen: Index },
    FieldStatus: { screen: FieldStatus },
    Scores: { screen: ScoreReporter },
    Schedule: { screen: Schedule },
    Sports: { screen: SportMain }
});

export default App;
