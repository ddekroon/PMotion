const handleErrors = response => {
  if (!response.ok) {
    console.log(response)
    throw Error(response.statusText)
  }

  return response
}

export default {
  handleErrors
}
