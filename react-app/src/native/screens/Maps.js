import React from 'react'
import MapView, {PROVIDER_GOOGLE, Marker} from 'react-native-maps'
import MapViewDirections from 'react-native-maps-directions'
import {Text, Container, Header, Body, Button } from 'native-base'
import {StyleSheet} from 'react-native'
import MapHelpers from '../../utils/maphelpers'

const GOOGLE_MAPS_APIKEY = 'AIzaSyCoKB5__7kMmOLTaICW9EtcbBjnuSlbdew';

export default class Map extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
        venue: this.props.route?.params?.venue ?? -1,
        coordinates: {
            latitude: 0,
            longitude: 0,
        },
        directions: {
            displayDirections: false,
            locationFetched: false,
            disablePress: true
        } 
    }
    this.onValueChange = this.onValueChange.bind(this);
  }

  componentDidMount(){
    //get current position of mobile device
    navigator.geolocation.getCurrentPosition(
        position => {
            this.setState({
                coordinates: {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                },
                directions: {
                    locationFetched: true,
                    displayDirections: false,
                    disablePress: false,
                }
            });
        },
        error => Alert.alert(error.message),
        { enableHighAccuracy: true, timeout: 20000, maximumAge: 1000 }
    );
  }

  onValueChange = (value) => {
    this.setState({
      venue: value
    });
  }

  render() {

    var marker = MapHelpers.getVenueObject(this.state.venue);
    var testLocations = ['U of Guelph', 'Lourdes', 'Springdale', 'Wilson Farm'];
  
    return (

        <Container>
            <Header style={{height: 50}}>
                <Body style={styles.header}> 
                    {
                        this.state.directions.locationFetched == true && marker.adress != 'No Location Found' &&
                        <Button disabled={this.state.directions.disablePress} style={styles.button} info onPress={() => this.setState({
                                directions: {
                                    displayDirections: true, 
                                    locationFetched: true,
                                    disablePress: true,
                                }
                            })}>
                            <Text>Get Directions</Text>
                        </Button>
                    }
                    <Text style={styles.adress}>{marker.adress}</Text>
                </Body>
            </Header>
            <MapView 
            provider={PROVIDER_GOOGLE}
            style={{flex: 1}} 
            initialRegion={{latitude: 43.5448,longitude: -80.2482,latitudeDelta: 0.15,longitudeDelta: 0.15}} 
            showsUserLocation={true} 
            >
                <Marker
                coordinate={marker.coordinates}
                title={marker.title}
                />
                
                {
                    this.state.directions.locationFetched == true && this.state.directions.displayDirections == true &&
                    <MapViewDirections
                    origin={this.state.coordinates}
                    destination={marker.coordinates}
                    apikey={GOOGLE_MAPS_APIKEY}
                    strokeWidth={4}
                    strokeColor='#4A89F3'
                    />
                }


            </MapView>
        </Container>
    )
  }
}

const styles = StyleSheet.create({
   adress: {fontWeight: 'bold', fontSize: 14},
   button: {height: 30},
   header: {height: 60}
}); 



/**
 *
                      {
                        this.state.directions.locationFetched == true &&
                        <Picker
                            note={false}
                            mode="dropdown"
                            iosIcon={<Icon name="arrow-down" />}
                            style={{ flex: 1 }}
                            selectedValue={"U of Guelph"}
                            iosHeader="Location"
                            onValueChange={(val, index) => {
                                this.onValueChange(val);
                            }}
                        >
                            <Picker.Item key={0} label="Location" value="" />
                            {testLocations.map(location => {
                            return (
                                <Picker.Item
                                key={location}
                                label={location}
                                value={location}
                                />
                            )
                            })}
                        </Picker>
                    }
 */