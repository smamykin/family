import {baseUrl} from "../../src/config";

let axios = require( '../../libraries/axios');

export class DeleteOffer
{
    deleteOffer(delete_url)
    {
        axios.delete(baseUrl + delete_url).then((response) => {
            console.log(response);
        }).catch((error) => {
            console.log(error);
        });
    }
}
