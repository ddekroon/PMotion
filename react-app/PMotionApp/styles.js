import { StyleSheet } from 'react-native';

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
        // backgroundColor: '#292929',
    },
    logo: {
        flex: 1,
        minWidth: 80,
        minHeight: 20,
        maxWidth: 400,
        maxHeight: 100,
        justifyContent: 'center',
    },
    header: {
    	height: '10%',
    	width: '100%',
    	backgroundColor: '#fff',
    	alignItems: 'center',
    },
    mainHeader: {
        fontSize: 40,
        fontWeight: 'bold',
    },
    content: {
    	height: '75%',
    	width: '100%',
    	// justifyContent: 'center',
    	flexWrap: 'wrap',
    	backgroundColor: '#ababab',
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
     	width: '100%',
     	height: '50%',
     	justifyContent: 'center',
     	alignItems: 'center',
    },
    buttonText: {
        color: '#fff',
        fontWeight: 'bold',
    },
    buttonContainer: {
    	width: '50%',
    	height:'100%',
    	justifyContent: 'center',
    	alignItems: 'center',
    	// flex: 1,
    	backgroundColor: '#272727',
    	padding: 5,
    },
});
