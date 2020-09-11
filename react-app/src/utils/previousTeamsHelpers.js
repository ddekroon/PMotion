import React from 'react'
import {Text} from 'native-base'
import { View} from 'react-native'
import { TouchableHighlight } from 'react-native-gesture-handler'

//If the user wants to sort by what teams are currently registered this runs.
export function organizeList(list) {
    var indexOfNextYes = 0;
    var arr = []

    list.map(elem => { //should be reduce as well?
       
        if (elem.isRegistered == "Yes") {
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
        
        if (elem.isRegistered == "No") {
            arr.splice(indexOfNextYes, 0, elem)
            indexOfNextYes++;
        } else {
            arr.push(elem)
        }
    })

    return arr
}

//Set it back to the default order
export function setListToDefault(list, oldLeagues) {
    var newList = [];

    oldLeagues.map((oldLeague) => {
        list.map((elem) => {
            if (oldLeague.teamName == elem.teamName && oldLeague.league == elem.league) {
                newList.push(elem)
                return;
            }
        })
    })

    return newList
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

//Update the leaguelist the is used when the user searches for a team
export function updateSearch(text, oldLeagues) {
    searchedLeague = []

    oldLeagues.map( (element) => {  //Should be reduce?
        

        if (element.teamName.includes(text) || element.league.includes(text) ) {

            var copy = element
            copy.key = searchedLeague.length
            searchedLeague.push(copy)
            console.log('elem name = ' + copy.teamName)
        }
    });
    return searchedLeague
}

export function addLeague(array, name, league, season, reg, route) {
    var elem = new Object

    //Load its attributes
    elem.key = array?.length?array.length:0
    elem.teamName = name;
    elem.league = league;
    elem.isRegistered = reg
    elem.season = season

    //Find if and where it's going to navigate to
    if (route?.params?.use == 'reregister') {
        elem.nav = true
        elem.navigationLocation = 'RegisterNewTeam'
        elem.sport = route.params.sport
    
    } else {
        elem.nav = false;
    }   

    array.push(elem)
}

//Helper for finding the background colour for each element in the previous teams list.
export function getAlternatingColours(num){
        
    if (num % 2 == 0) {
        return'#DCDCDC'
    } 
    return  'white'
}