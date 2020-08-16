import status from './status';
import member from './member';
import lookups from './lookups';
import scoreSubmission from './scoreSubmission';
import leagues from './leagues';
import teams from './teams';
import Waiver from './Waivers';
import Login from './Login';

const rehydrated = (state = false, action) => {
  switch (action.type) {
    case 'persist/REHYDRATE':
      return true;
    default:
      return state;
  }
};

export default {
  rehydrated,
  status,
  member,
  lookups,
  scoreSubmission,
  leagues,
  teams,
  Waiver,
  Login,
};
