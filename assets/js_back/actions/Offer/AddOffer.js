import {baseUrl} from "../../src/config";

let axios = require( '../../libraries/axios');

export class AddOffer {
    addOffer(url, price, priceCurrency, product_id)
    {
        let params = {
            url,
            price,
            priceCurrency,
            "product": "api/products/" + product_id
        }

        console.log(params);

        let config = {
            headers: {
                'Accept': 'application/ld+json',
                'Content-Type': 'application/ld+json'
            }
        }

        axios.post(baseUrl + '/api/offers', params, config).then((response) => {
            console.log(response);
        }).catch((error) => {
            console.log(error);
        });
    }
}
