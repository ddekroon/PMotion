const handleErrors = response => {
  console.log("errors = " + JSON.stringify(response))
  if (!response.ok) {
    console.log(response)
    throw Error(response.statusText)
  }

  return response
}

export default {
  handleErrors
}
