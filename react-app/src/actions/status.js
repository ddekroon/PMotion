import Enums from '../constants/enums'

/**
  * Show Message - currently not used anywhere
  */
export default function (dispatch, type, message) {
  return new Promise((resolve, reject) => {
    // Validate types
    const allowed = Object.values(Enums.messageTypes).filter((curType) => curType != Enums.messageTypes.None);

    if (!allowed.includes(type)) {
      return reject('Type should be one of: ' + allowed.join(', '));
    }

    // Set some defaults for convenience
    if (!message) {
      if (type === 'success') message = 'Success';
      if (type === 'error') message = 'Sorry, an error occurred';
      if (type === 'info') message = 'Something is happening...';
    }

    return resolve(dispatch({
      type: 'STATUS_REPLACE',
      message: {
        type: type,
        message: message
      }
    }));
  });
}
