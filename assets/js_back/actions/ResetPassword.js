import {baseUrl, email,password} from "../src/config";

let axios = require( '../libraries/axios');

export class ResetPassword {
    makeRequest()
    {
        let params = {
            email
        }
        let config = {
            headers: {
                'accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }

        return axios.post(baseUrl + '/api/users/reset-password', params, config).then(response => {
            return response.data;
        }).catch((error) => {

        }) ;
    }

    resetPassword(userId, token)
    {
        let config = {
            headers: {
                "Accept": 'application/ld+json',
                'Content-Type': 'application/merge-patch+json'
            }
        };
        axios.patch(baseUrl + '/api/users/' + userId + '/change-password?token=' + token + '&password=' + password, {}, config).then(response => {
            console.log(response);
            localStorage.removeItem('userIdChangesPassword');
        }).catch(error=>{
            console.error(error);
        });
    }
}

