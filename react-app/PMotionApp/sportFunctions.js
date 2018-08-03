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

export const getSportLogo = (sportID)=>{
	let sportLogo;

	switch(sportID) {
        case 1:
            sportLogo = require("./img/ultimate_0.png");
            break;
        case 2:
            sportLogo = require("./img/volleyball_0.png");
            break;
        case 3:
            sportLogo = require("./img/football_0.png");
            break;
        case 4:
            sportLogo = require("./img/soccer_0.png");
            break;
        default:
            sportLogo = require("./img/ultimate_0.png"); // Shouldn't ever default
            break;
    }

    return sportLogo;
};

export const getReadySet = (sportID)=>{
	let readySet;

	switch(sportID) {
        case 1:
            readySet = require("./img/ready-set-play-ultimate.png");
            break;
        case 2:
            readySet = require("./img/ready-set-play-volleyball.png");
            break;
        case 3:
            readySet = require("./img/ready-set-play-football.png");
            break;
        case 4:
            readySet = require("./img/ready-set-play-soccer.png");
            break;
        default:
            readySet = require("./img/ready-set-play-soccer.png"); // Shouldn't ever default
            break;
    }

    return readySet;
};
