import { baseURL } from './config.js';
import { show } from './view.js';
import {pagination} from "./pagination.js";
import {router} from "./router.js";

let axios = require( '../libraries/axios');

export const getData = (event) => {
    let url = router(event)
    axios.get(baseURL + url)
        .then(function (response) {
            const orderByName = document.getElementById("order-by-name");
            const filterWithImages = document.getElementById('filter-with-images-only');

            orderByName.addEventListener('click', getData);
            filterWithImages.addEventListener('click', getData);

            orderByName.style.display = 'block';
            filterWithImages.style.display = 'block';

            if (typeof orderByName.order === 'undefined' ) {
                orderByName.order = 'asc';
            } else if (orderByName.order === 'asc') {
                orderByName.order = 'desc';
            } else {
                orderByName.order = 'asc';
            }

            pagination(response.data);
            show(response.data['hydra:member']);
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });

}

