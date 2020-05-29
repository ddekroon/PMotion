import React, {useState} from 'react'
import { Container, Content, Text, Picker, Icon, View } from 'native-base'

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
            alignItems: 'center',
            flexDirection:'row',
            justifyContent: 'center',
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
