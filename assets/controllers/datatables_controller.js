import { Controller } from '@hotwired/stimulus';
import DataTable from 'datatables.net-dt';
import 'datatables.net-columncontrol-dt';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {


    table = new DataTable('#table', {
        columnControl: [
            {
                extend: 'order',
                columns: ':gt(0)'
            },
            [{ extend: 'orderAsc' }, { extend: 'orderDesc' }]
        ],

        responsive: true,
        //  LocalStorage est automatiser grâce à stateSave
        stateSave: true,
        layout: {
            topEnd: {
                search: {
                    placeholder: "Rechercher"
                }
            },

        },
        select: true,
        language: {
            processing: "Traitement en cours...",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
            info: "Affichage de _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Aucun résultat",
            infoFiltered: "(filtrage sur _MAX_ &eacute;l&eacute;ments )",
            infoPostFix: "",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });
}

