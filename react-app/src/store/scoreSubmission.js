 import Enums from '../constants/enums';

export default {
  submitting: false,
  submitted: false,
  sportId: '',
  leagueId: '',
  teamId: '',
  dateId: '',
  matches: [
    {
      oppTeamId: '',
      results: [
        {
          result: Enums.matchResult.Error.val,
          scoreUs: '',
          scoreThem: ''
        },
        {
          result: Enums.matchResult.Error.val,
          scoreUs: '',
          scoreThem: ''
        }
      ],
      spiritScore: '',
      comment: ''
    },
    {
      oppTeamId: '',
      results: [
        {
          result: Enums.matchResult.Error.val,
          scoreUs: '',
          scoreThem: ''
        },
        {
          result: Enums.matchResult.Error.val,
          scoreUs: '',
          scoreThem: ''
        }
      ],
      spiritScore: '',
      comment: ''
    }
  ],
  name: '',
  email: ''
};