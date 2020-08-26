import React, {useState} from 'react'
import { Container, Content, Text, Picker, Icon, View } from 'native-base'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
/**
 * Expects all sports given in an array (given to props: 'sports'), where each sport is an object modeled after:
 * const Soccer = {
    "name":"Soccer",
    "id":4
  }
 */

class PickTeam extends React.Component {
  static propTypes = {
    sports: PropTypes.array.isRequired
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
          selectedValue = {this.props.chsnSport}
          onValueChange = {(val) => this.props.func(val)}
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

const mapStateToProps = state => ({
  sports: state.lookups.sports || []
})


export default connect(
  mapStateToProps,
)(PickTeam)

