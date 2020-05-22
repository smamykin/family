import { baseURL } from './config.js';
import { show } from './view.js';
import {pagination} from "./pagination.js";
import {router} from "./router.js";

let axios = require( '../libraries/axios');

export const getData = (event) => {
    let url = router(event)
    axios.get(baseURL + url)
        .then(function (response) {
            console.log(response);
            pagination(response.data);
            show(response.data['hydra:member']);
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });

}

