import {baseUrl, email, password} from '../src/config'

let axios = require( '../libraries/axios');

export class Login {
    getJWTToken()
    {
        let params = {
            email,
            password
        };
        let config = {
            headers: {
                accept: 'application/json',
            }
        };

        axios.post(baseUrl+'/authentication_token', params, config).then(response=>{
            localStorage.setItem("jwt_token", response.data.token)
            // console.log(response.data.token);
        }).catch(error => {
            // console.log(error);
        })

    }

    logout()
    {
        localStorage.removeItem('jwt_token');
    }
}
