import { Toast } from 'native-base';
import Enums from '../constants/enums'

export default {
	showToast: (type, message, duration = 2500) => {
		var toastType = 'default';
		switch (type) {
			case Enums.messageTypes.Success: {
				toastType = 'success'
				break;
			}
			case Enums.messageTypes.Error: {
				toastType = 'danger'
				break;
			}
			case Enums.messageTypes.Alert: {
				toastType = 'warning'
				break;
			}
		}

		Toast.show({
			text: message,
			duration,
			position: 'bottom',
			buttonText: 'Okay',
			type: toastType
		});
	},
};