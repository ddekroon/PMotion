import Enums from '../constants/enums';

export default {
  sportId: '',
  leagueId: '',
  teamId: '',
  dateId: '',
  matches: [
    {
      oppTeamId: '',
      results: [
        {
          result: Enums.matchResult.Error,
          scoreUs: '',
          scoreThem: ''
        },
        {
          result: Enums.matchResult.Error,
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
          result: Enums.matchResult.Error,
          scoreUs: '',
          scoreThem: ''
        },
        {
          result: Enums.matchResult.Error,
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