import {baseUrl} from "../../src/config";
import {Login} from "../Login";

let axios = require( '../../libraries/axios');

export class AddOffer extends Login  {

    constructor(url,price,priceCurrency,product_id)
    {
        super('addOffer')
        this.url = url;
        this.price = price;
        this.priceCurrency = priceCurrency;
        this.productID = product_id;
    }
    addOffer()
    {
        let params = {
            url: this.url,
            price: this.price,
            priceCurrency: this.priceCurrency,
            "product": "api/products/" + this.productID
        }

        let config = {
            headers: {
                'Accept': 'application/ld+json',
                'Content-Type': 'application/ld+json'
            }
        }

        axios.post(baseUrl + '/api/offers', params, config).then((response) => {
            console.log(response);
        }).catch((error) => {
            if (error.response.data.code == '401') {
                this.handle401Error();
            }
        });
    }
}
