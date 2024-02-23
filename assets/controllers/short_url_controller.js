import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    open(response) {
        this.inputTarget.value = response.redirect_link;
        this.element.classList.remove('hide');
    }
}