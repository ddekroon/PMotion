//Doesn't need anything passed to it, but somehow needs to know the users previous teams

import React, { useState} from 'react'
import {Text, Picker, Icon, Container, Content, Card} from 'native-base'
import {Button, View, Image} from 'react-native'

import {StyleSheet} from 'react-native'
import { TextInput, ScrollView, TouchableHighlight } from 'react-native-gesture-handler'

var rowSize = 30
var searchedLeague = []
var oldLeagues = []
var count = 0;

//The colour part of this is still broken ;/
export default function Previousleagues({navigation, route}) {
    if (oldLeagues.length != 0) {
        oldLeagues = [];
    }
    
    const [chosen, setChosen] = useState(10)
    const [pageNum, setPageNum] = useState(1)
    const [beingShown, setBeingShown] = useState(oldLeagues)
    const [checker, setchecker] = useState(0)
    
    //Testers to see if if it works with teams **Can be deleted

    addLeague(oldLeagues, 'name1', 'league1', 'season1', 'No', route, navigation)      //1
    addLeague(oldLeagues, 'name2', 'league2', 'season2', 'No', route, navigation)      //2
    addLeague(oldLeagues, 'name3', 'league3', 'season3', 'No', route, navigation)      //3
    addLeague(oldLeagues, 'name4', 'league4', 'season4', 'Yes', route, navigation)     //4
    addLeague(oldLeagues, 'name5', 'league5', 'season5', 'No', route, navigation)      //5
    addLeague(oldLeagues, 'name6', 'league6', 'season6', 'Yes', route, navigation)     //6
    addLeague(oldLeagues, 'name7', 'league7', 'season7', 'Yes', route, navigation)     //7
    addLeague(oldLeagues, 'name8', 'league8', 'season8', 'No', route, navigation)      //8
    addLeague(oldLeagues, 'name9', 'league9', 'season9', 'Yes', route, navigation)     //9
    addLeague(oldLeagues, 'name10', 'league10', 'season10', 'No', route, navigation)   //10
    addLeague(oldLeagues, 'name11', 'league11', 'season11', 'Yes', route, navigation)  //11
    addLeague(oldLeagues, 'name12', 'league12', 'season12', 'Yes', route, navigation)  //12
    addLeague(oldLeagues, 'name13', 'league13', 'season13', 'Yes', route, navigation)  //13

    return (
        <Container>
            <Content>
                <Card>
                    <ScrollView>
                        {/**Header */}
                        <Text style={styles.header}>Teams Previously Registered</Text>
                        <Text style={styles.subheading}>Click on your team's name to re-register a previous team for the upcoming season</Text>
                        <View style={{paddingBottom:5}}/>
                        <View style={styles.line}/>
                        <View style={{paddingBottom:15}}/>

                        {/*How many elements being shown on the page */}
                        <View style={styles.splitEnds}>
                            <View style={styles.sideBySide}>
                                <Text>Show</Text>
                                <Picker
                                    placeholder='10'
                                    mode="dropdown"
                                    iosIcon={<Icon name="arrow-down" />}
                                    style={ styles.picker}
                                    selectedValue = {chosen}
                                    onValueChange={ (amount) => {
                                        //Set the chosen amount per page and reset the page number to the first page
                                        setChosen(amount), 
                                        setPageNum(1)
                                    } }
                                >
                                    <Picker.Item label="10" value="10" key={1}/>
                                    <Picker.Item label="25" value="25" key={2}/>
                                    <Picker.Item label="50" value="50" key={3}/>
                                    <Picker.Item label="100" value="100" key={4}/>
                                </Picker>

                                <Text>entries</Text>
                            </View>

                            <View style={styles.sideBySide}>
                                {/* Allow the user to search through all the previous teams */}
                                <Text>Search</Text>
                                <TextInput 
                                    style={styles.search}
                                    onChangeText={text => { 

                                        updateSearch(text)
                                        if (text.length != 0) {
                                            //setShow(true)
                                            setBeingShown(searchedLeague)
                                            setPageNum(1)
                                        } else {
                                            //setShow(false)
                                            setBeingShown(oldLeagues)
                                            setPageNum(1)
                                        }
                                    }}
                                />
                            </View>
                        </View>

                        <View style={{paddingBottom:10}}/>
                        <View style={{flexDirection:'row', width:'95%'}}>
                            {/* Headers for all the leagues being shown */}
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>Name</Text>
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>League</Text>
                            <Text style={styles.first, {fontWeight:'bold', width:'25%', textAlign:'center'}}>Season</Text>
                            
                            {/*Made the view clickable so you can sort by if they have been registered */}
                            <TouchableHighlight onPress={() => {
                                //This makes it so the user can keep clicking and getting different organizations each time (yes first, no first, and original order)
                                if (checker == 0 ) {
                                    setBeingShown(organizeList(oldLeagues))
                                    setchecker(1)   // The reason this is here is because  when I use 'if (beingShown == organizeList(oldLeagues))' it always returns false and I don't get it                

                                } else if (checker == 1) {
                                    setchecker(2)
                                    setBeingShown(organizeListReverse(oldLeagues))

                                } else {
                                    setchecker(0)
                                    setBeingShown(oldLeagues)
                                }

                                setPageNum(1)
                            }}>
                                <View style={{flexDirection:'row', alignItems:'left', justifyContent:'left'}}>
                                    <Image
                                        source={require('../../images/icons/list.png')}
                                    />
                                    <Text style={styles.first, {fontWeight:'bold'}}>Registered</Text>
                                </View>
                            </TouchableHighlight>
                        </View>

                        <View style={{ height:( beingShown.length > 0 && chosen < beingShown.length? chosen*rowSize: beingShown.length*rowSize), borderWidth:3, overflow:'hidden'}}>
                            {/** The expression for height shows either the chosen amount out of the total group, or if the chosen amount is bigger than the group,  just the full group */}
                            
                            {
                                //display only the elements on the chosen page form the array
                                count = -1,
                                beingShown.map( (elem) => {
                                    if (beingShown.indexOf(elem) < (pageNum-1)*(chosen) || beingShown.indexOf(elem) >= (pageNum) * (chosen)) {
                                    } else {
                                        count++
                                        
                                        return (
                                            <View key={count}j>
                                                {elem}
                                            </View >
                                        )
                                    }
                                    count += 1
                                })
                            }

                        </View>

                        <View style={{paddingBottom:10}}/>
                        <View>
                            <View style={{ 
                                opacity: Math.ceil(beingShown.length/chosen) > 1 ? 100: 0,
                                flexDirection:'row',
                                justifyContent: 'center',
                                alignItems: 'center',
                            }}>

                                {/* the bottoms at the bottom for chosing the page number and go forwards/backwards a page*/}
                                <Button title={'Previous'} color={ (pageNum == 1? 'grey' : 'red') } onPress={ () => setPageNum(previousPress(pageNum)) }/>
                                <Button title={ '' + getFirst(pageNum, Math.ceil(beingShown.length/chosen)) } color={ (pageNum == 1? 'grey' : 'red') } onPress={() => setPageNum(firstPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                                
                                <View style = { Math.ceil(beingShown.length/chosen) > 1 ? styles.show : styles.hide}>
                                    <Button title={ '' + getSecond(pageNum, Math.ceil(beingShown.length/chosen)) } color={ (pageNum > 1 && pageNum < Math.ceil(beingShown.length/chosen) - 1) || pageNum == 2 ? 'grey' : 'red' } onPress={() => setPageNum(secondPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                                </View>

                                <View style = { Math.ceil(beingShown.length/chosen) > 2 ? styles.show : styles.hide}>
                                    <Button title={ '' + getThird(pageNum, Math.ceil(beingShown.length/chosen)) } color={ (thirdIsGrey(pageNum, Math.ceil(beingShown.length/chosen)) ? 'grey' : 'red') } onPress={() => setPageNum(thirdPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                                </View>

                                <View style = { Math.ceil(beingShown.length/chosen) > 3 ? styles.show : styles.hide}>
                                    <Button title={ '' + getFourth(pageNum, Math.ceil(beingShown.length/chosen)) } color={ (pageNum >= Math.ceil(beingShown.length/chosen)? 'grey' : 'red') } onPress={() => setPageNum(fourthPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                                </View>

                                <Button title={'Next'} color={ (pageNum >= Math.ceil(beingShown.length/chosen)? 'grey' : 'red') } onPress={() => setPageNum(nextPress(pageNum, Math.ceil(beingShown.length/chosen))) }/>
                            </View>
                        </View>

                        {/**Show the page number */}
                        <View>
                            <Text style={styles.subheading}>Page {pageNum} of {Math.ceil(beingShown.length/chosen)} entries.</Text>
                        </View>
                    </ScrollView>
                </Card>
            </Content>
        </Container>
    )
}

//If the user wants to sort by what teams are currently registered this runs.
export function organizeList(list) {
    var indexOfNextYes = 0;
    var arr = []

    list.map(elem => {
        var name = elem.props.children[3].props.children
        if (name == "Yes") {
            arr.splice(indexOfNextYes, 0, elem)
            indexOfNextYes++;
        } else {
            arr.push(elem)
        }
    })

    return arr
}

//Same thing as above but in reverse
export function organizeListReverse(list) {
    var indexOfNextYes = 0;
    var arr = []

    list.map(elem => {
        var name = elem.props.children[3].props.children
        if (name == "No") {
            arr.splice(indexOfNextYes, 0, elem)
            indexOfNextYes++;
        } else {
            arr.push(elem)
        }
    })

    return arr
}

//Function to make the color of the 'third' button black
export function thirdIsGrey(pg, numPages) {

    if (numPages == 3 || numPages == 4 ) {
        if (pg == 3) {
            return true
        }

    } else if (pg == numPages -1) {
        return true
    } 
    return false;
}

//      ***What each button does when clicked
export function previousPress(pg) {
    var returnedVal
    if (pg == 1) {
        returnedVal = pg
    } else {
        returnedVal = pg-1
    }

    return returnedVal
}

export function firstPress(pg, numPages) {
    var returnedVal
    if (pg == 1) {
        returnedVal = pg
    
    } else if (numPages - pg == 0) {
        returnedVal = pg-3
    
    } else if (numPages - pg == 1) {
        returnedVal = pg-2
    
    } else if (numPages - pg == 2) {
        returnedVal = pg-3
    
    } else {
        returnedVal = pg-1
    }
    
    return returnedVal
}

export function secondPress(pg, numPages) {
    var returnedVal
    if (pg == 1) {
        if (numPages > pg) {
            returnedVal = pg + 1
        }
    } else if (numPages - pg == 0) {
        returnedVal = pg - 2

    } else if (numPages - pg == 1) {
        returnedVal = pg - 1

    } else {
        returnedVal = pg
    }
    
    return returnedVal
}

export function thirdPress(pg, numPages) {
    var returnedVal
    if (pg == 1) {
        if (numPages - pg == 0) {
            returnedVal = pg
        
        } else if (numPages - pg == 1) {
            returnedVal = pg

        } else {
            returnedVal = pg + 2
        }

    } else {
        if (numPages - pg == 2) {
            returnedVal = pg + 1
        
        } else if (numPages - pg == 1) {
            returnedVal = pg + 1
        
        } else {
            returnedVal = pg 
        }
    }
    
    return returnedVal
}

export function fourthPress(pg, numPages) {
    var returnedVal
    if (pg == 1) {
        if (numPages - 2 > pg) {
            returnedVal = pg + 2
        }

    } else if (numPages - pg == 1) {
        returnedVal = pg + 1
        
    } else if (numPages - 2 > pg) {
        returnedVal = pg + 1
    
    } else {
        returnedVal = pg
    }
    
    return returnedVal
    
}

export function nextPress(pg, numPages) {
    var returnedVal
    if (numPages > pg) {
        returnedVal = pg + 1;
    } else {
        returnedVal = pg
    }
    
    return returnedVal
}

//      ***The number that is diplayed on each button
export function getFirst(pg, numPages) {
    if (numPages <= 4) {
        return 1
    }
    
    var returnedAmount;
    if (pg == 1) {
        returnedAmount = pg
    } else {
        if (numPages - pg == 1) { 
            returnedAmount = pg-2
    
        } else if (numPages - pg == 0) {
            returnedAmount = pg-3
        } else {
            returnedAmount = pg-1
        }
    }

    return returnedAmount
}
export function getSecond(pg, numPages) {
    var returnedAmount;
    if (numPages <= 4) {
        return 2
    }

    if (pg == 1) {
        returnedAmount = pg+1
    } else {
        if (numPages - pg == 1) { 
            returnedAmount = pg-1
    
        } else if (numPages - pg == 0) {
            returnedAmount = pg-2
        } else {
            returnedAmount = pg
        }
    }
    return returnedAmount
}
export function getThird(pg, numPages) {

    if (numPages <= 4) {
        return 3
    }
        
    var returnedAmount;
    if (pg == 1) {
        returnedAmount = pg+2
    } else {
        if (numPages - pg == 1) { 
            returnedAmount = pg
    
        } else if (numPages - pg == 0) {
            returnedAmount = pg-1
        } else {
            returnedAmount = pg+1
        }
    }
    return returnedAmount
}
export function getFourth(pg, numPages) {
    var returnedAmount;
    if (numPages <= 4) {
        return 4
    }

    if (pg == 1) {
        returnedAmount = pg+3
    } else {
        if (numPages - pg == 1) { 
            returnedAmount = pg +1
    
        } else if (numPages - pg == 0) {
            returnedAmount = pg
        } else {
            returnedAmount = pg+2
        } 
    }
    return returnedAmount
}

//*********************Shits a tad fucky ngl */

JSON.decycle = function decycle(object, replacer) {
    "use strict";

    var objects = new WeakMap();     // object to path mappings

    return (function derez(value, path) {

        var old_path;   // The path of an earlier occurance of value
        var nu;         // The new object or array

        if (replacer !== undefined) {
            value = replacer(value);
        }

        if (
            typeof value === "object"
            && value !== null
            && !(value instanceof Boolean)
            && !(value instanceof Date)
            && !(value instanceof Number)
            && !(value instanceof RegExp)
            && !(value instanceof String)
        ) {

            old_path = objects.get(value);
            if (old_path !== undefined) {
                return {$ref: old_path};
            }

            objects.set(value, path);

            if (Array.isArray(value)) {
                nu = [];
                value.forEach(function (element, i) {
                    nu[i] = derez(element, path + "[" + i + "]");
                });
            } else {

                nu = {};
                Object.keys(value).forEach(function (name) {
                    nu[name] = derez(
                        value[name],
                        path + "[" + JSON.stringify(name) + "]"
                    );
                });
            }
            return nu;
        }
        return value;
    }(object, "$"));
};



if (typeof JSON.retrocycle !== "function") {
    JSON.retrocycle = function retrocycle($) {
    "use strict";


    var px = /^\$(?:\[(?:\d+|"(?:[^\\"\u0000-\u001f]|\\(?:[\\"\/bfnrt]|u[0-9a-zA-Z]{4}))*")\])*$/;

    (function rez(value) {

        if (value && typeof value === "object") {
            if (Array.isArray(value)) {
                value.forEach(function (element, i) {
                    if (typeof element === "object" && element !== null) {
                        var path = element.$ref;
                        if (typeof path === "string" && px.test(path)) {
                            value[i] = eval(path);
                        } else {
                            rez(element);
                        }
                    }
                });
            } else {
                Object.keys(value).forEach(function (name) {
                    var item = value[name];
                    if (typeof item === "object" && item !== null) {
                        var path = item.$ref;
                        if (typeof path === "string" && px.test(path)) {
                            value[name] = eval(path);
                        } else {
                            rez(item);
                        }
                    }
                });
            }
        }
    }($));
    return $;
};
}


//*********************Shits a tad fucky ngl */


//Update the leaguelist the is used when the user searches for a team
export function updateSearch(text) {
    searchedLeague = []

    //console.log("oldLeagues = " + JSON.stringify(oldLeagues))

    oldLeagues.map( (element) => {
        //This line finds the name from the element in the oldLeagues list (of old teams)
        var name = element.props.children[0].props.children

        console.log("text = " + text)
        console.log("name = " + JSON.decycle(name))

        if (name.includes(text)) {

            var copy = element
            copy.key = searchedLeague.length
            searchedLeague.push(copy)
        }
    });

}

//adds all the past teams pulled from the server into JSX elements that can be displayed
export function addLeague(array, name, league, season, reg, route, navigation) {
    var elem
    let colour

    if (count % 2 == 0) {
        colour = '#DCDCDC'
    } else {
        colour = 'white'
    }
    count++;

    if (route?.params?.use == 'reregister') {
        elem = (
            <View style={{height:rowSize, justifyContent: 'space-between', flexDirection:'row', backgroundColor:colour}} key={array.length} >
                <TouchableHighlight onPress={() => {
                    navigation.navigate('RegisterNewTeam', {
                        team: {
                            name:name,
                            league:league,
                        },
                        sport:route.params.sport,
                        //More vals will get past once I make server requests
                    })
                }}>
                    <Text style={{fontSize:20, textAlign:'center', color:'red'}}>{name}</Text>
                </TouchableHighlight>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{league}</Text>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{season}</Text>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{reg}</Text>
            </View>
        )
    } else {
        
        elem = (
            <View style={{height:rowSize, justifyContent: 'space-between', flexDirection:'row', backgroundColor:colour}} key={array.length} >
                <Text style={{fontSize:20, textAlign:'center', color:'#383838'}}>{name}</Text>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{league}</Text>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{season}</Text>
                <Text style={{fontSize:20, textAlign:'center', width:'25%', color:'#383838'}}>{reg}</Text>
            </View>
        )
    }    
    
    elem.key = array.length    
    array.push(elem);
}

//Styles
const styles = StyleSheet.create ({
    elem: {
        height:rowSize,
        justifyContent: 'space-between',
        borderWidth:1,
    },

    first: {
        fontSize:20,
        textAlign:'center',
        width:'25%'
    },

    header: {
        fontWeight:'bold',
        fontSize:35
    },

    subHeading: {
        color: '#474747',
        fontSize:12,
    },

    line: {
        borderBottomColor:'black',
        borderBottomWidth:1,
    },

    sideBySide: {
        flexDirection:'row',
        justifyContent: 'center',
        alignItems: 'center',
    },

    splitEnds: {
        flexDirection:'row',
        justifyContent:'space-between',
    },

    search: {
        borderBottomWidth:1,
        borderBottomColor:'red',
        width:120,
    },

    picker: {
        width:100,
        borderWidth:1,
    },

    show: {
        opacity:100
    },

    hide: {
        opacity:0
    },
    
})