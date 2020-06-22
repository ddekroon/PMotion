import React, {useState} from 'react'
import { Container, Content, Text, Picker, Icon, View } from 'native-base'

/**
 * Expects all sports given in an array (given to props: 'sports'), where each sport is an object modeled after:
 * const Soccer = {
    "name":"Soccer",
    "id":4
  }
 */

export default class PickTeam extends React.Component {

  state = {sport: ''}
  updateSport = (sport) => {
      this.setState({ sport: sport })
  }
  render() {
    return (
      <View>
        <Picker
          note={false}
          mode="dropdown"
          iosIcon={<Icon name="arrow-down" />}
          style = {{
            borderWidth: 1,
            
            //Should be centered :/
          }}

          placeholder = "Sport"
          selectedValue = {this.state.sport}
          onValueChange = {this.updateSport}
        >
          <Picker.Item key={0} label="Sport" value="Sport"/>

          {this.props.sports.map(curSport => {              
            return (
              <Picker.Item
                key={curSport.id}
                label={curSport.name}
                value={curSport.id}
              />
            )   
          })}
        </Picker>
      </View>    
    )
  }
}
