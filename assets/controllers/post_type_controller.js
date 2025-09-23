import { Controller } from '@hotwired/stimulus';


export default class extends Controller {

    static targets = ["row"];


    filterPost(event) {
        console.log(event.target.index);
        console.log(event.type);
        console.log(this.targets.dataset);
        console.log(event.target.value);

        let post = event.target.value;

        // Récupérations de toute les targets (row)
        this.rowTargets.forEach(row => {
            // row represente une ligne du tableau de donnée (un post)
            let rowType = row.dataset.typeId
            console.log("Type de l'id", rowType)

            if (post === rowType || !post) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
}