import { Platform, StyleSheet } from 'react-native';

export default StyleSheet.create({
	container: {
        flex: 1,
        backgroundColor: '#fff',
        // alignItems: 'center',
        // justifyContent: 'flex-start',
    },
    logoContainer: {
    	height: '25%',
        width: '100%',
        paddingVertical: 5,
        paddingHorizontal: 10,
        alignItems: 'center',
        justifyContent: 'center',
    },
    mainLogo: {
        resizeMode: 'contain',
        width: '100%',
    },
    subLogo: {
        width: '100%',
        height: '20%',
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
        // fontFamily: 'JockeyOne-Regular', // Custom font may require setup on XCode and Android Studio - may not need this custom font if I replace titles/headers with images+logos
    },
    mainContent: {
    	height: '70%',
    	width: '100%',
    	// justifyContent: 'center',
    	flexWrap: 'wrap',
    	backgroundColor: '#272727',
        borderTopRightRadius: 4,
        borderTopLeftRadius: 4,
    },
    mainText: {
    },
    sportsContent: {
    	height: '75%',
    	width: '100%',
    	backgroundColor: '#272727',
        borderTopRightRadius: 4,
        borderTopLeftRadius: 4,
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
        borderRadius: 4,
    	// flex: 1,
    	backgroundColor: '#272727',
    	padding: '2%',
    },
    buttonContainerSingle: {
    	height: '100%',
    	width: '100%',
    	justifyContent: 'center',
    	alignItems: 'center',
        borderRadius: 4,
    	backgroundColor: '#272727',
    	padding: 5,
    },
    sportButtonContainer: { // UNUSED
        width: '50%',
        height: '100%',
        justifyContent: 'center',
    },
    sportButton: {
        borderRadius: 4,
        borderBottomWidth: 8,
        padding: 3,
        width: '98%',
        height: '70%',
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#fff',
    },
    sportLogo: {
        resizeMode: 'contain',
        width: '100%',
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
    offlineErrorView: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    offlineErrorText: {
        textAlign: 'center',
    },
});
