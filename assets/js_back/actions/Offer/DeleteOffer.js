import {baseUrl} from "../../src/config";
import {Login} from "../Login";

let axios = require( '../../libraries/axios');

export class DeleteOffer extends Login
{
    constructor(delete_url) {
        super('deleteOffer');
        this.delete_url = delete_url;
    }

    deleteOffer()
    {
        axios.delete(baseUrl + this.delete_url).then((response) => {
            console.log(response);
        }).catch((error) => {
            if (error.response.data.code == '401') {
                this.handle401Error();
            }
        });
    }
}
