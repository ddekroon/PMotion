import React from 'react'
import MapView, {PROVIDER_GOOGLE, Marker} from 'react-native-maps'
import {Text, Container, Card, CardItem, Header, Body} from 'native-base'
import {StyleSheet} from 'react-native'

export default class Map extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
        latitude: 0,
        longitude: 0,
    }
  }

  componentDidMount(){
    this.watchId = navigator.geolocation.watchPosition(
        (position) => {
            this.setState({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
            });
        },
        (error) => {
            this.setState({error: error.message})
        },
        {enableHighAccuracy: false, timeout: 1, maximumAge:1, distanceFilter: 1}
    )
  }

  render() {

    const venue = this.props.navigation.getParam('venue');

    console.log("lat:" + this.state.latitude + " " + "long:" + this.state.longitude);

    var marker = {
        title: '',
        adress:'',
        coordinates: {
            latitute: 0,
            longitude: 0,
        }
    }

    if(venue.includes("U of Guelph")){
        marker.title = 'U of Guelph Beach Volleyball';
        marker.coordinates.latitude = 43.531402;
        marker.coordinates.longitude = -80.219921;
        marker.adress = '';
    }else if(venue.includes("Guelph Lake")){
        marker.title = 'Guelph Lake Sports Fields';
        marker.coordinates.latitude = 43.586793;
        marker.coordinates.longitude = -80.255051;
        marker.adress = 'Woodlawn Road East, Guelph, ON';
    }else if(venue.includes("Margaret") || venue.includes("Margaret Green")){
        marker.title = 'Margaret Green Park';
        marker.coordinates.latitude = 43.530584;
        marker.coordinates.longitude = -80.281722;
        marker.adress = '80 Westwood Rd, Guelph, ON';
    }else if(venue.includes("SilverCreek")){
        marker.title = 'SilverCreek Park';
        marker.coordinates.latitude = 43.535198;
        marker.coordinates.longitude = -80.251958;
        marker.adress = '';
    }else if(venue.includes("Bailey")){
        marker.title = 'Bailey Park';
        marker.coordinates.latitude = 43.558906;
        marker.coordinates.longitude = -80.274895;
        marker.adress = '55 Bailey Ave';
    }else if(venue.includes("Lourdes")){
        marker.title = 'Lourdes High School';
        marker.coordinates.latitude = 43.547769;
        marker.coordinates.longitude = -80.267713;
        marker.adress = '54 Westmount Rd, Guelph, ON N1H 5H7';
    }else if(venue.includes("Springdale")){
        marker.title = 'Springdale Park';
        marker.coordinates.latitude = 43.519301;
        marker.coordinates.longitude = -80.274616;
        marker.adress = '38 Springdale Blvd,Guelph, ON N1H 7S5';
    }else if(venue.includes("Wilson Farm")){
        marker.title = 'Wilson Farm';
        marker.coordinates.latitude = 43.579715;
        marker.coordinates.longitude = -80.267982;
        marker.adress = '80 Simmonds Dr, Guelph, ON N1E 0G4';
    }else if(venue.includes("Centennial")){
        marker.title = 'Centennial';
        marker.coordinates.latitude = 43.521831;
        marker.coordinates.longitude = -80.250960;
        marker.adress = '371 College Ave W, Guelph, ON N1G 1T3';
    }else if(venue.includes("Dovercliffe")){
        marker.title = 'Dovercliffe Park';
        marker.coordinates.latitude = 43.512281;
        marker.coordinates.longitude = -80.251991;
        marker.adress = '38 Dovercliffe Rd, Guelph, ON';
    }else if(venue.includes("Grange Park")){
        marker.title = 'Grange Park';
        marker.coordinates.latitude = 43.574491;
        marker.coordinates.longitude = -80.224425;
        marker.adress = '598 Grange Rd, Guelph, ON N1E 7C6';
    }else if(venue.includes("Castlebury")){
        marker.title = 'Castlebury Park';
        marker.coordinates.latitude = 43.529456;
        marker.coordinates.longitude = -80.272227;
        marker.adress = '50 Castlebury Dr, Guelph, ON N1K 1X2';
    }else if(venue.includes("Eastview")){
        marker.title = 'Eastview Community Park';
        marker.coordinates.latitude = 43.581814;
        marker.coordinates.longitude = -80.233555;
        marker.adress = '186 Eastview Rd, Guelph, ON N1E 6X2';
    }else if(venue.includes("W.E Hamilton")){
        marker.title = 'W.E Hamilton Park';
        marker.coordinates.latitude = 43.518429;
        marker.coordinates.longitude = -80.242075;
        marker.adress = '565 Scottsdale Dr, Guelph, ON N1G 2W6';
    }else if(venue.includes("Severn Drive")){
        marker.title = 'Severn Drive Park';
        marker.coordinates.latitude = 43.576045;
        marker.coordinates.longitude = -80.217148;
        marker.adress = '125 Severn Dr, Guelph, ON N1E 7K6';
    }else if(venue.includes("Bishop Mac")){
        marker.title = 'Bishop Macdonald High School';
        marker.coordinates.latitude = 43.494527;
        marker.coordinates.longitude = -80.195389;
        marker.adress = '200 Clair Rd W, Guelph, ON N1L 1G1';
    }else if(venue.includes("Herb Markle")){
        marker.title = 'Herb Markle Park';
        marker.coordinates.latitude = 43.553219;
        marker.coordinates.longitude = -80.256160;
        marker.adress = '175 Cardigan St, Guelph, ON N1H 3Z8';
    }

  
    return (

        <Container>
            <Header style={{height: 50}}>
                <Body>
                    <Text style={styles.marker}>{marker.adress}</Text>
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

            </MapView>
        </Container>
    )
  }
}

const styles = StyleSheet.create({
   marker: {fontWeight: 'bold', fontSize: 16},
}); 
