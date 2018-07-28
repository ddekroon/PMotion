export const getSportName = (sportID)=>{
    let sportName;
    // console.warn("Finding sportName for sportID: " + sportID);

    switch(sportID) {
        case 1:
            sportName = "Ultimate";
            break;
        case 2:
            sportName = "Volleyball";
            break;
        case 3:
            sportName = "Football";
            break;
        case 4:
            sportName = "Soccer";
            break;
        default:
            sportName = "Ultimate";
            break;
    }

    return sportName;
};
