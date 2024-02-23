import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form','shortUrl'];

    connect() {
        this.formTarget.addEventListener('submit',  function(e) {
            e.preventDefault();

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

        switch(response.valid_response) {
            case true:
                this.handleShortUrl(response)
                break;
            case false:
                this.handleErrors(response.errors)
                break;
        }
    }

    handleShortUrl(response) {
        let short_url_controller = this.application.getControllerForElementAndIdentifier(
            this.shortUrlTarget,
            'short-url'
        );

        this.hide();
        short_url_controller.open(response);
    }

    handleErrors(errors) {
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

    removeErrors() {
        const isInvalidElements = document.querySelectorAll('.is-invalid');
        const invalidFeedbackElements = document.querySelectorAll('.invalid-feedback');

        isInvalidElements.forEach(isInvalid => isInvalid.classList.remove('is-invalid'));
        invalidFeedbackElements.forEach(invalidFeedback=> invalidFeedback.remove());
    }

    hide() {
        this.formTarget.classList.add('hide');
    }
}