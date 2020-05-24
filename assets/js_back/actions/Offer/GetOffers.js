import {baseUrl} from "../../src/config";
import {Login} from "../Login";

let axios = require( '../../libraries/axios');

export class GetOffers extends Login
{
    constructor() {
        super('getOffers');
    }

    getOffers() {
        let userId = localStorage.getItem('user_id')
        if (userId == null) return;
        axios.get(baseUrl + '/api/users/'+userId+'/offers').then( response => {
            response.data['hydra:member'].forEach(offer => {
                this.addOptionElement(offer.url, offer['@id'])
            });
        }).catch(error => {
            // console.log(error);
            this.handle401Error();
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
