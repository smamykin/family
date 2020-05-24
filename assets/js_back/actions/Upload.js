import {Login} from "./Login";
import {baseUrl} from "../src/config";

let axios = require( '../libraries/axios');

export class Upload extends Login {
    constructor(files, productId) {
        super('upload');
        this.files = files;
        this.productId = productId;
    }

    upload()
    {
        let data = new FormData();
        data.append('imageFile', this.files[0], this.files[0].name);
        data.append('product_id', this.productId);

        let config = {
            headers: {
                'content-type': 'multipart/form-data'
            }
        }
        axios.post(baseUrl + '/api/upload', data, config).then(response => {
            console.log(response);
        }).catch(error => {
            console.log(error);
            if (error.response.data.code == '401') {
                this.handle401Error();
            }
        })

    }

}
