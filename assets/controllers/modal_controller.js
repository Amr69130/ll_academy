import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ["modal"]

    openModal(event) {
        // ICI ON SELECTIONNE LE CONTENEUR INTERNE
        let modalBox = this.modalTarget.querySelector("div")

        modalBox.innerHTML = `
            <img src="/images/${event.target.dataset.flag}" alt="${event.target.dataset.name}" class="mb-2 w-24 h-16 object-cover rounded">
            <h3 class="text-xl font-bold mb-2">${event.target.dataset.name}</h3>
            <p class="text-gray-700">${event.target.dataset.description}</p>
            <p class="text-gray-700">${event.target.dataset.isopen}</p>
            <p class="text-gray-700">${event.target.dataset.price}</p>           
                             `
        this.modalTarget.classList.remove("hidden")
    }

    closeModal(event) {
        if (event.target === this.modalTarget) {
            this.modalTarget.classList.add("hidden")
            this.modalTarget.querySelector("div").innerHTML = ""
        }
    }
}
