'use strict';

import React, { Component } from 'react';
import { createStackNavigator } from 'react-navigation';
import Index from './index.js';
import ScoreReporter from './scoreReport.js';
import Schedule from './schedule.js';
import SportMain from './sportMain.js';

const App = createStackNavigator({
    Home: { screen: Index },
    Scores: { screen: ScoreReporter },
    Schedule: { screen: Schedule },
    Sports: { screen: SportMain }
});

export default App;
