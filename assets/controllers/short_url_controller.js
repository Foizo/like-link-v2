import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form','shortcut', 'shortAnotherUrl'];

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

        this.shortAnotherUrlTarget.addEventListener('click',  function(e) {
            e.preventDefault();
            this.open();
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

        this.hide();
        this.application.getControllerForElementAndIdentifier(
            this.shortcutTarget,
            'shortcut'
        ).open(response);
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

    open() {
        this.application.getControllerForElementAndIdentifier(
            this.shortcutTarget,
            'shortcut'
        ).hide();
        this.formTarget.reset();
        this.formTarget.classList.remove('hide');
        this.formTarget.classList.remove('unhide');
    }

    hide() {
        this.formTarget.classList.add('hide');
    }
}