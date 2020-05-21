import { baseURL } from './config.js';
import { show } from './view.js';

let axios = require( '../libraries/axios');

export const getData = () => {
// console.log(router(event));
    axios.get(baseURL + '/api/products')
        .then(function (response) {
            show(response.data['hydra:member']);
            // console.log(response.data);
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });

}

