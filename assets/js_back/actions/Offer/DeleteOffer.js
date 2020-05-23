import {baseUrl} from "../../src/config";

let axios = require( '../../libraries/axios');

export class DeleteOffer
{
    constructor() {
        axios.defaults.headers.common = {
            'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
        }
    }

    deleteOffer(delete_url)
    {
        axios.delete(baseUrl + delete_url).then((response) => {
            console.log(response);
        }).catch((error) => {
            console.log(error);
        });
    }
}
