import {baseUrl} from "../../src/config";

let axios = require( '../../libraries/axios');

export class GetOffers {
    constructor() {
        axios.get(baseUrl + '/api/offers').then( response => {
            response.data['hydra:member'].forEach(offer => {
                this.addOptionElement(offer.url, offer['@id'])
            });
        }).catch(error => {
            console.log(error);
        })
    }
    addOptionElement(text, value)
    {
        let option = document.createElement("option");
        option.text = text;
        option.value = value;
        let select = document.getElementById('delete-offer');
        select.appendChild(option);
    }
}
