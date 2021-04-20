const form = document.querySelector('form[name="profile_request"]')
if (form) {
    form.addEventListener('submit', event => {
        event.preventDefault()
        const formData = new FormData(form)
        const messageDiv = document.querySelector('.message')
        console.log(formData)
        fetch(
            UPLOAD_FORM_PATH,
            {
                method: 'POST',
                body: formData
            }
        )
            .then(response => response.json())
            .then(response => {
                if(response.message) {
                    messageDiv.innerHTML = response['message']
                    form.reset()
                } else {
                    messageDiv.innerHTML = 'Error: ' + response['error']
                }
            })
            .catch(error => {
                console.log(error)
            })
    } )
}