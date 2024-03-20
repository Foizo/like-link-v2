import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'spinner', 'message'];

    connect() {
        this.formTarget.addEventListener('submit',  function(e) {
            e.preventDefault();
            this.spinnerTarget.style.display = 'inline-block';
            fetch(this.formTarget.action, {
                body: new FormData(e.target),
                method: 'POST'
            })
                .then(response => response.json())
                .then(json => {
                    this.handleResponse(json);
                });
        }.bind(this));
    };


    handleResponse(response) {
        this.removeErrors();
        this.spinnerTarget.style.display = 'none';

        switch(response.valid_response) {
            case true:
                this.handleSuccess(response)
                break;
            case false:
                this.handleErrors(response.errors)
                break;
        }
    }

    handleSuccess(response) {
        this.messageTarget.style.display = 'inline-block';
        setTimeout(
            function () {
                this.formTarget.reset();
                this.messageTarget.style.display = 'none';
            }.bind(this),
            3000
        );
    }

    handleErrors(errors) {
        if (errors.length === 0) {
            return;
        }

        for (const key in errors) {
            let element = document.querySelector(`#contact_form_${key}`)
            element.classList.add('is-invalid');

            let div = document.createElement('div');
            div.classList.add('invalid-feedback', 'd-block');
            div.innerText = errors[key];

            element.after(div);
        }
    }

    removeErrors() {
        const isInvalidElements = document.querySelectorAll('.is-invalid');
        const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');

        isInvalidElements.forEach(isInvalid => isInvalid.classList.remove('is-invalid'));
        invalidFeedbackElements.forEach(invalidFeedback=> invalidFeedback.remove());
    }
}