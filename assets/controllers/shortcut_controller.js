import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    open(response) {
        this.inputTarget.value = response.redirect_link;
        this.element.classList.remove('hide');
        this.element.classList.add('unhide');
    }

    hide() {
        this.element.classList.remove('unhide');
        this.element.classList.add('hide');
    }
}