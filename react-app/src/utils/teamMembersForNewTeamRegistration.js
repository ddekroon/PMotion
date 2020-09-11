export function getTeamMembers(arrayOfPlayers) {

    if (arrayOfPlayers == undefined) arrayOfPlayers = [];   //Just incase no players were given, prevents crashing
    let obj = new Object;

    obj.playerID_0  =           null
    obj.playerFirstName_0  =    arrayOfPlayers[0]?.fn?arrayOfPlayers[0].fn:null
    obj.playerLastName_0  =     arrayOfPlayers[0]?.ln?arrayOfPlayers[0].ln:null
    obj.playerEmail_0  =        arrayOfPlayers[0]?.email?arrayOfPlayers[0].email:null
    obj.playerPhone_0  =        arrayOfPlayers[0]?.phone?arrayOfPlayers[0].phone:null
    obj.playerGender_0  =       arrayOfPlayers[0]?.gender?arrayOfPlayers[0].gender:'M'

    obj.playerID_1=             null
    obj.playerFirstName_1  =    arrayOfPlayers[1]?.fn?arrayOfPlayers[1].fn:null 
    obj.playerLastName_1 =      arrayOfPlayers[1]?.ln?arrayOfPlayers[1].ln:null
    obj.playerEmail_1 =         arrayOfPlayers[1]?.email?arrayOfPlayers[1].email:null
    obj.playerPhone_1 =         arrayOfPlayers[1]?.phone?arrayOfPlayers[1].phone:null
    obj.playerGender_1 =        arrayOfPlayers[1]?.gender?arrayOfPlayers[1].gender:'M'

    obj.playerID_2 =            null
    obj.playerFirstName_2  =    arrayOfPlayers[2]?.fn?arrayOfPlayers[2].fn:null
    obj.playerLastName_2  =     arrayOfPlayers[2]?.ln?arrayOfPlayers[2].ln:null  
    obj.playerEmail_2 =         arrayOfPlayers[2]?.email?arrayOfPlayers[2].email:null
    obj.playerPhone_2 =         arrayOfPlayers[2]?.phone?arrayOfPlayers[2].phone:null
    obj.playerGender_2 =        arrayOfPlayers[2]?.gender?arrayOfPlayers[2].gender:'M'

    obj.playerID_3 =            null
    obj.playerFirstName_3  =    arrayOfPlayers[3]?.fn?arrayOfPlayers[3].fn:null 
    obj.playerLastName_3  =     arrayOfPlayers[3]?.ln?arrayOfPlayers[3].ln:null
    obj.playerEmail_3 =         arrayOfPlayers[3]?.email?arrayOfPlayers[3].email:null
    obj.playerPhone_3 =         arrayOfPlayers[3]?.phone?arrayOfPlayers[3].phone:null
    obj.playerGender_3 =        arrayOfPlayers[3]?.gender?arrayOfPlayers[3].gender:'M'

    obj.playerID_4 =            null
    obj.playerFirstName_4  =    arrayOfPlayers[4]?.fn?arrayOfPlayers[4].fn:null 
    obj.playerLastName_4 =      arrayOfPlayers[4]?.ln?arrayOfPlayers[4].ln:null
    obj.playerEmail_4 =         arrayOfPlayers[4]?.email?arrayOfPlayers[4].email:null
    obj.playerPhone_4 =         arrayOfPlayers[4]?.phone?arrayOfPlayers[4].phone:null
    obj.playerGender_4 =        arrayOfPlayers[4]?.gender?arrayOfPlayers[4].gender:'M'

    obj.playerID_5 =            null
    obj.playerFirstName_5  =    arrayOfPlayers[5]?.fn?arrayOfPlayers[5].fn:null 
    obj.playerLastName_5 =      arrayOfPlayers[5]?.ln?arrayOfPlayers[5].ln:null
    obj.playerEmail_5 =         arrayOfPlayers[5]?.email?arrayOfPlayers[5].email:null
    obj.playerPhone_5 =         arrayOfPlayers[5]?.phone?arrayOfPlayers[5].phone:null
    obj.playerGender_5 =        arrayOfPlayers[5]?.gender?arrayOfPlayers[5].gender:'M'

    obj.playerID_6 =            null
    obj.playerFirstName_6  =    arrayOfPlayers[6]?.fn?arrayOfPlayers[6].fn:null 
    obj.playerLastName_6 =      arrayOfPlayers[6]?.ln?arrayOfPlayers[6].ln:null
    obj.playerEmail_6 =         arrayOfPlayers[6]?.email?arrayOfPlayers[6].email:null
    obj.playerPhone_6 =         arrayOfPlayers[6]?.phone?arrayOfPlayers[6].phone:null
    obj.playerGender_6 =        arrayOfPlayers[6]?.gender?arrayOfPlayers[6].gender:'M'

    obj.playerID_7 =            null
    obj.playerFirstName_7  =    arrayOfPlayers[7]?.fn?arrayOfPlayers[7].fn:null 
    obj.playerLastName_7 =      arrayOfPlayers[7]?.ln?arrayOfPlayers[7].ln:null
    obj.playerEmail_7 =         arrayOfPlayers[7]?.email?arrayOfPlayers[7].email:null
    obj.playerPhone_7 =         arrayOfPlayers[7]?.phone?arrayOfPlayers[7].phone:null
    obj.playerGender_7 =        arrayOfPlayers[7]?.gender?arrayOfPlayers[7].gender:'M'

    obj.playerID_8 =            null
    obj.playerFirstName_8  =    arrayOfPlayers[8]?.fn?arrayOfPlayers[8].fn:null 
    obj.playerLastName_8 =      arrayOfPlayers[8]?.ln?arrayOfPlayers[8].ln:null
    obj.playerEmail_8 =         arrayOfPlayers[8]?.email?arrayOfPlayers[8].email:null
    obj.playerPhone_8 =         arrayOfPlayers[8]?.phone?arrayOfPlayers[8].phone:null
    obj.playerGender_8 =        arrayOfPlayers[8]?.gender?arrayOfPlayers[8].gender:'M'

    obj.playerID_9 =            null
    obj.playerFirstName_9  =    arrayOfPlayers[9]?.fn?arrayOfPlayers[9].fn:null 
    obj.playerLastName_9 =      arrayOfPlayers[9]?.ln?arrayOfPlayers[9].ln:null
    obj.playerEmail_9 =         arrayOfPlayers[9]?.email?arrayOfPlayers[9].email:null
    obj.playerPhone_9 =         arrayOfPlayers[9]?.phone?arrayOfPlayers[9].phone:null
    obj.playerGender_9 =        arrayOfPlayers[9]?.gender?arrayOfPlayers[9].gender:'M'

    obj.playerID_10 =           null
    obj.playerFirstName_10  =   arrayOfPlayers[10]?.fn?arrayOfPlayers[10].fn:null 
    obj.playerLastName_10 =     arrayOfPlayers[10]?.ln?arrayOfPlayers[10].ln:null
    obj.playerEmail_10 =        arrayOfPlayers[10]?.email?arrayOfPlayers[10].email:null
    obj.playerPhone_10 =        arrayOfPlayers[10]?.phone?arrayOfPlayers[10].phone:null
    obj.playerGender_10 =       arrayOfPlayers[10]?.gender?arrayOfPlayers[10].gender:'M'

    obj.playerID_11 =           null
    obj.playerFirstName_11  =   arrayOfPlayers[11]?.fn?arrayOfPlayers[11].fn:null 
    obj.playerLastName_11 =     arrayOfPlayers[11]?.ln?arrayOfPlayers[11].ln:null
    obj.playerEmail_11 =        arrayOfPlayers[11]?.email?arrayOfPlayers[11].email:null
    obj.playerPhone_11 =        arrayOfPlayers[11]?.phone?arrayOfPlayers[11].phone:null
    obj.playerGender_11 =       arrayOfPlayers[11]?.gender?arrayOfPlayers[11].gender:'M'

    obj.playerID_12 =           null
    obj.playerFirstName_12  =   arrayOfPlayers[12]?.fn?arrayOfPlayers[12].fn:null 
    obj.playerLastName_12 =     arrayOfPlayers[12]?.ln?arrayOfPlayers[12].ln:null
    obj.playerEmail_12 =        arrayOfPlayers[12]?.email?arrayOfPlayers[12].email:null
    obj.playerPhone_12 =        arrayOfPlayers[12]?.phone?arrayOfPlayers[12].phone:null
    obj.playerGender_12 =       arrayOfPlayers[12]?.gender?arrayOfPlayers[12].gender:'M'

    obj.playerID_13 =           null
    obj.playerFirstName_13  =   arrayOfPlayers[13]?.fn?arrayOfPlayers[13].fn:null 
    obj.playerLastName_13 =     arrayOfPlayers[13]?.ln?arrayOfPlayers[13].ln:null
    obj.playerEmail_13 =        arrayOfPlayers[13]?.email?arrayOfPlayers[13].email:null
    obj.playerPhone_13 =        arrayOfPlayers[13]?.phone?arrayOfPlayers[13].phone:null
    obj.playerGender_13 =       arrayOfPlayers[13]?.gender?arrayOfPlayers[13].gender:'M'

    obj.playerID_14 =           null
    obj.playerFirstName_14  =   arrayOfPlayers[14]?.fn?arrayOfPlayers[14].fn:null
    obj.playerLastName_14 =     arrayOfPlayers[14]?.ln?arrayOfPlayers[14].ln:null
    obj.playerEmail_14 =        arrayOfPlayers[14]?.email?arrayOfPlayers[14].email:null
    obj.playerPhone_14 =        arrayOfPlayers[14]?.phone?arrayOfPlayers[14].phone:null
    obj.playerGender_14 =       arrayOfPlayers[14]?.gender?arrayOfPlayers[14].gender:'M'

    return obj
}
