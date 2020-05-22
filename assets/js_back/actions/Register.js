import {baseUrl, email, password} from '../src/config'

let axios = require( '../libraries/axios');
export class Register {

    static register()
    {
        let params = new URLSearchParams()
        params.append('email', email)
        params.append('password', password)

        let config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        }

        axios.post(baseUrl+'/register', params, config).then((response) => {
            console.log(response.data)
        }).catch((error) => {
            console.log(error)
        })
    }
}
