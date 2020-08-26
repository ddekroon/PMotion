/**
 * 
 * 
 *  Don't think this file actually runs
 * 
 * 
 */


import React, {useState} from 'react'
import PropTypes from 'prop-types' 
import { connect } from 'react-redux'
import {Picker, View, Icon } from 'native-base'
import { fetchLeague } from '../../../actions/leagues' //Gets the leagues from the web.
import DateTimeHelpers from '../../../utils/datetimehelpers'

//Expects a sport id given as 1-4 under the 'sport' props tag

class PickLeagues extends React.Component { 
  
  //I make a server call to get all the leagues. In the props (sport=(1-4)) the correct leagues for that sport appear
  static propTypes = {
    seasons: PropTypes.object.isRequired,
    leagues: PropTypes.object.isRequired,
    getLeague: PropTypes.func.isRequired 
  }
  
  constructor(props) {
    super(props)
    state = {
      counter:[1]
    }
  }

  render() {
    
    const {
      seasons
    } = this.props

    if (seasons == null || seasons == undefined) console.log("Error loading seasons");

    return (
      <View>
        <Picker
          placeholder="League"
          note={false}
          mode="dropdown"
          iosIcon={<Icon name="arrow-down" />}
          selectedValue = {this.props.league}
          onValueChange={(elem) => {
            if (elem == 1 || elem == undefined || elem == null) return;
            
            let leagueName
            seasons[this.props.sport][0].leagues.map(curLeague => {
              if (curLeague.id == elem) {   
                leagueName =
                  curLeague.name +
                  ' - ' +
                  DateTimeHelpers.getDayString(curLeague.dayNumber)
                return
              }
            })
            this.props.update(this.props.index, leagueName)
          }}
        >

          <Picker.Item key={0} label={'League'} value={''} />
          {/**Leaving this bit in incase I find a bug, If i don't ill delete it*/}
          {/* this.props.sport != 'Sport' ? seasons[this.props.sport][0].leagues.map(curLeague => {
            var leagueName =
              curLeague.name +
              ' - ' +
              DateTimeHelpers.getDayString(curLeague.dayNumber)

            return (
              <Picker.Item
                key={curLeague.id}
                label={leagueName}
                value={curLeague.id}
              />
            )
          }):<Picker.item key={0} label={'*No sport chosen*'} value={''}/>*/}
          
          {this.props.sport != 'Sport' ?
            seasons[this.props.sport][0].leagues.reduce(curLeague => reducer(curLeague))
            :<Picker.item key={0} label={'*No sport chosen*'} value={''}/>
          }

        </Picker>
      </View>
    )
  }
}

function reducer(curLeague) {
  var leagueName =
    curLeague.name +
    ' - ' +
    DateTimeHelpers.getDayString(curLeague.dayNumber);

  console.log("The sport ID = " + this.props.sport)
  
  return (
    <Picker.Item
      key={curLeague.id}
      label={leagueName}
      value={curLeague.id}
    />
  )
}

const mapStateToProps = state => ({
  seasons: state.lookups.scoreReporterSeasons || [],
  leagues: state.leagues || {},
})

const mapDispatchToProps = { 
  getLeague: fetchLeague
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(PickLeagues)
