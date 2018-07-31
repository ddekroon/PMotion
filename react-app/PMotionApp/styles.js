import { Platform, StyleSheet } from 'react-native';

export default StyleSheet.create({
	container: {
        flex: 1,
        backgroundColor: '#fff',
        alignItems: 'center',
        justifyContent: 'flex-start',
    },
    logoContainer: {
    	height: '15%',
        padding: 5,
    },
    logo: {
        flex: 1,
        minWidth: 80,
        minHeight: 20,
        // maxWidth: 400,
        // maxHeight: 100,
        justifyContent: 'center',
    },
    header: {
    	height: '30%',
    	width: '100%',
    	backgroundColor: '#fff',
    	alignItems: 'center',
    },
    mainHeader: {
        fontSize: 40,
        // fontWeight: 'bold', // Can't use this with custom font
        alignItems: 'center',
        fontFamily: 'JockeyOne-Regular', // Custom font may require setup on XCode and Android Studio
    },
    mainContent: {
    	height: '50%',
    	width: '100%',
    	// justifyContent: 'center',
    	flexWrap: 'wrap',
    	backgroundColor: '#fff',
    },
    mainText: {
    },
    sportsContent: {
    	height: '70%',
    	width: '100%',
    	backgroundColor: '#fff',
    },
    contentRow: {
    	flex: 1,
    	flexDirection: 'row',
    	justifyContent: 'center',
    	alignItems: 'center',
    },
    webStyle: {
        flex: 1,
    },
    button: {
        borderRadius: 4,
        backgroundColor: '#de1219',
        paddingHorizontal: 16,
        paddingVertical: 8,
        elevation: 2,
     	width: '90%',
     	height: '50%',
     	justifyContent: 'center',
     	alignItems: 'center',
    },
    buttonText: {
        color: '#fff',
        fontWeight: 'bold',
        fontSize: 15,
    },
    buttonContainer: {
    	width: '50%',
    	height:'100%',
    	justifyContent: 'center',
    	alignItems: 'center',
    	// flex: 1,
    	backgroundColor: '#272727',
    	padding: '2%',
    },
    buttonContainerSingle: {
    	height: '100%',
    	width: '100%',
    	justifyContent: 'center',
    	alignItems: 'center',
    	backgroundColor: '#272727',
    	padding: 5,
    },
    footer: {
    	height: '5%',
    	width: '100%',
    	justifyContent: 'center',
    	alignItems: 'center',
    	backgroundColor: '#272727',
    },
    mainFooter: {
    	color: '#fff',
    	fontSize: 12,
    	alignItems: 'center',
    },
});
