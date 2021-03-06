export default {
	getVenueObject: (venue) => {

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
            return marker;
        }else if(venue.includes("Guelph Lake")){
            marker.title = 'Guelph Lake Sports Fields';
            marker.coordinates.latitude = 43.586793;
            marker.coordinates.longitude = -80.255051;
            marker.adress = 'Woodlawn Road East, Guelph, ON';
            return marker;
        }else if(venue.includes("Margaret") || venue.includes("Margaret Greene")){
            marker.title = 'Margaret Greene Park';
            marker.coordinates.latitude = 43.530584;
            marker.coordinates.longitude = -80.281722;
            marker.adress = '80 Westwood Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("SilverCreek")){
            marker.title = 'SilverCreek Park';
            marker.coordinates.latitude = 43.535198;
            marker.coordinates.longitude = -80.251958;
            marker.adress = '';
            return marker;
        }else if(venue.includes("Bailey")){
            marker.title = 'Bailey Park';
            marker.coordinates.latitude = 43.558906;
            marker.coordinates.longitude = -80.274895;
            marker.adress = '55 Bailey Ave, Guelph, ON';
            return marker;
        }else if(venue.includes("Lourdes")){
            marker.title = 'Lourdes High School';
            marker.coordinates.latitude = 43.547769;
            marker.coordinates.longitude = -80.267713;
            marker.adress = '54 Westmount Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("Springdale")){
            marker.title = 'Springdale Park';
            marker.coordinates.latitude = 43.519301;
            marker.coordinates.longitude = -80.274616;
            marker.adress = '38 Springdale Blvd,Guelph, ON';
            return marker;
        }else if(venue.includes("Wilson Farm")){
            marker.title = 'Wilson Farm';
            marker.coordinates.latitude = 43.579715;
            marker.coordinates.longitude = -80.267982;
            marker.adress = '80 Simmonds Dr, Guelph, ON';
            return marker;
        }else if(venue.includes("Centennial")){
            marker.title = 'Centennial';
            marker.coordinates.latitude = 43.521831;
            marker.coordinates.longitude = -80.250960;
            marker.adress = '371 College Ave W, Guelph, ON';
            return marker;
        }else if(venue.includes("Dovercliffe")){
            marker.title = 'Dovercliffe Park';
            marker.coordinates.latitude = 43.512281;
            marker.coordinates.longitude = -80.251991;
            marker.adress = '38 Dovercliffe Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("Grange Park")){
            marker.title = 'Grange Park';
            marker.coordinates.latitude = 43.574491;
            marker.coordinates.longitude = -80.224425;
            marker.adress = '598 Grange Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("Castlebury")){
            marker.title = 'Castlebury Park';
            marker.coordinates.latitude = 43.529456;
            marker.coordinates.longitude = -80.272227;
            marker.adress = '50 Castlebury Dr, Guelph, ON';
            return marker;
        }else if(venue.includes("Eastview")){
            marker.title = 'Eastview Community Park';
            marker.coordinates.latitude = 43.581814;
            marker.coordinates.longitude = -80.233555;
            marker.adress = '186 Eastview Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("W.E Hamilton")){
            marker.title = 'W.E Hamilton Park';
            marker.coordinates.latitude = 43.518429;
            marker.coordinates.longitude = -80.242075;
            marker.adress = '565 Scottsdale Dr, Guelph, ON';
            return marker;
        }else if(venue.includes("Severn Drive")){
            marker.title = 'Severn Drive Park';
            marker.coordinates.latitude = 43.576045;
            marker.coordinates.longitude = -80.217148;
            marker.adress = '125 Severn Dr, Guelph, ON';
            return marker;
        }else if(venue.includes("Bishop Mac")){
            marker.title = 'Bishop Macdonald High School';
            marker.coordinates.latitude = 43.494527;
            marker.coordinates.longitude = -80.195389;
            marker.adress = '200 Clair Rd W, Guelph, ON';
            return marker;
        }else if(venue.includes("Herb Markle")){
            marker.title = 'Herb Markle Park';
            marker.coordinates.latitude = 43.553219;
            marker.coordinates.longitude = -80.256160;
            marker.adress = '175 Cardigan St, Guelph, ON';
            return marker;
        }else if(venue.includes("O’Conner")){
            marker.title = 'O’Conner Park';
            marker.coordinates.latitude = 43.570239;
            marker.coordinates.longitude = -80.219873;
            marker.adress = "31 O'Connor Ln, Guelph, ON";
            return marker;
        }else if(venue.includes("Curling Club")){
            marker.title = 'Guelph Curling Club';
            marker.coordinates.latitude = 43.567111;
            marker.coordinates.longitude = -80.279525;
            marker.adress = '816 Woolwich St, Guelph, ON';
            return marker;
        }else if(venue.includes("Eramosa River")){
            marker.title = 'Eramosa River Park';
            marker.coordinates.latitude = 43.549544;
            marker.coordinates.longitude = -80.221834;
            marker.adress = '259 Victoria Rd S, Guelph, ON';
            return marker;
        }else if(venue.includes("Gateway Public")){
            marker.title = 'Gateway Public';
            marker.coordinates.latitude = 43.518041;
            marker.coordinates.longitude = -80.275046;
            marker.adress = '33 Gateway Dr, Guelph, ON ';
            return marker;
        }else if(venue.includes("Orin Reid")){
            marker.title = 'Orin Reid Park';
            marker.coordinates.latitude = 43.509834;
            marker.coordinates.longitude = -80.182303;
            marker.adress = '120 Goodwin Dr, Guelph ON';
            return marker;
        }else if(venue.includes("Marden Field House") || venue.includes("Marden")){
            marker.title = 'Marden Park';
            marker.coordinates.latitude = 43.534303;
            marker.coordinates.longitude = -80.224905;
            marker.adress = '55 E Ring Rd, Guelph, ON ';
            return marker;
        }else if(venue.includes("J.F. Ross")){
            marker.title = 'J.F. Ross';
            marker.coordinates.latitude = 43.561506;
            marker.coordinates.longitude = -80.246603;
            marker.adress = '21 Meyer Dr, Guelph, ON ';
            return marker;
        }else if(venue.includes("Peter Misersky")){
            marker.title = 'Peter Misersky Park';
            marker.coordinates.latitude = 43.564755;
            marker.coordinates.longitude = -80.231550;
            marker.adress = '122 Hadati Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("Rickson Park")){
            marker.title = 'Rickson Park';
            marker.coordinates.latitude = 43.520656;
            marker.coordinates.longitude = -80.224823;
            marker.adress = '25 Rickson Ave, Guelph, ON';
            return marker;
        }else if(venue.includes("St. Francis")){
            marker.title = 'St. Francis School';
            marker.coordinates.latitude = 43.524226;
            marker.coordinates.longitude = -80.281983;
            marker.adress = '287 Imperial Rd S, Guelph, ON ';
            return marker;
        }else if(venue.includes("UofG Football Stadium")){
            marker.title = 'UofG Football Stadium';
            marker.coordinates.latitude = 43.534793;
            marker.coordinates.longitude = -80.227703;
            marker.adress = '15 Lang Way, Guelph, ON ';
            return marker;
        }else if(venue.includes("UofG Varsity")){
            marker.title = 'UofG Varsity Field';
            marker.coordinates.latitude = 43.536972;
            marker.coordinates.longitude = -80.224368;
            marker.adress = '';
            return marker;
        }else if(venue.includes("UofG Field Hockey")){
            marker.title = 'UofG Field Hockey Field';
            marker.coordinates.latitude = 43.532958;
            marker.coordinates.longitude = -80.220343;
            marker.adress = '';
            return marker;
        }else if(venue.includes("Westminster Woods")){
            marker.title = 'Westminster Woods';
            marker.coordinates.latitude = 43.508133;
            marker.coordinates.longitude = -80.189929;
            marker.adress = '146 Clairfields Dr E, Guelph, ON';
            return marker;
        }else if(venue.includes("York Road")){
            marker.title = 'York Road Park';
            marker.coordinates.latitude = 43.541444;
            marker.coordinates.longitude = -80.238101;
            marker.adress = '85 York Rd, Guelph, ON';
            return marker;
        }else if(venue.includes("Exhibition")){
            marker.title = 'Exhibition Park';
            marker.coordinates.latitude = 43.549159;
            marker.coordinates.longitude = -80.262167;
            marker.adress = '81 London Rd W, Guelph, ON';
            return marker;
        }else{
            marker.title = '';
            marker.coordinates.latitute = 0;
            marker.coordinates.longitude = 0;
            marker.adress = 'No Location Found'
            return marker;
        }
	}
};