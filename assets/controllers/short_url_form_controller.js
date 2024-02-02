import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.form = document.querySelector('#short-url-form')
        this.form.addEventListener('submit',  function(e) {
            e.preventDefault();

            fetch(this.action, {
                body: new FormData(e.target),
                method: 'POST'
            })
                .then(response => response.json())
                .then(json => {
                   handleResponse(json);
                });
        })

        function handleResponse(response) {
            removeErrors();

            switch(response.valid_response) {
                case true:
                    console.log(response)
                    break;
                case false:
                    handleErrors(response.errors)
                    break;
            }
        }

        function handleErrors(errors) {
            if (errors.length === 0) {
                return;
            }

            for (const key in errors) {
                let element = document.querySelector(`#short_url_form_${key}`)
                element.classList.add('is-invalid');

                let div = document.createElement('div');
                div.classList.add('invalid-feedback', 'd-block');
                div.innerText = errors[key];

                element.after(div);
            }
        }

        function removeErrors() {
            const isInvalidElements = document.querySelectorAll('.is-invalid');
            const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');

            isInvalidElements.forEach(isInvalid => isInvalid.classList.remove('is-invalid'));
            invalidFeedbackElements.forEach(invalidFeedback=> invalidFeedback.remove());
        }
    };


}