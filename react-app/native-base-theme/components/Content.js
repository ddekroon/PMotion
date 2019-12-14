import variable from './../variables/platform'

export default (variables = variable) => {
  const contentTheme = {
    flex: 1,
    backgroundColor: 'transparent',
    'NativeBase.Segment': {
      borderWidth: 0,
      backgroundColor: 'transparent'
    },
    '.underline': {
      '.error': {
        borderColor: variables.inputErrorBorderColor
      },
      borderWidth: variables.borderWidth * 2,
      borderTopWidth: 0,
      borderRightWidth: 0,
      borderLeftWidth: 0,
      borderColor: variables.inputBorderColor
    }
  }

  return contentTheme
}
