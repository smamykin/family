import {Register} from './actions/Register';
import {Login} from "./actions/Login";
import {Products} from './actions/Products';
import {AddOffer} from './actions/Offer/AddOffer';
import {GetOffers} from "./actions/Offer/GetOffers";
import {DeleteOffer} from "./actions/Offer/DeleteOffer";
import {ResetPassword} from "./actions/ResetPassword";

// let login = new Login();
// login.getJWTToken();
window.onload = () => {
    let needsLogin = document.getElementById('needs-login');
    needsLogin.addEventListener('click', () => {
        let login = new Login();
        login.logout().then(() => {
            login.login();
        });
        needsLogin.style.display = 'none';
    });

};
// login.logout();
// offer
new Products
let addOffer = document.getElementById('add-offer');
addOffer.addEventListener('click', () => {
    let url = document.getElementById('url');
    let price = document.getElementById('price');
    let currency = document.getElementById('currency');
    let product = document.getElementById('product');
    let product_id = product.options[product.selectedIndex].value;
    new AddOffer(url.value,parseFloat(price.value),currency.value,product_id);
});

new GetOffers();
let deleteOffer = document.getElementById('delete-offer-button');
deleteOffer.addEventListener('click', () => {
    let offer = document.getElementById('delete-offer');
    let offer_id = offer.options[offer.selectedIndex].value;
    new DeleteOffer(offer_id);
});

let resetPasswordRequest = document.getElementById('reset-password-request');
resetPasswordRequest.addEventListener('click', () => {
   let resetP = new ResetPassword();
   resetP.makeRequest().then(response => {
       if (typeof response !==  'undefined') {
           window.localStorage.setItem('userIdChangesPassword', response);
           resetPasswordRequest.style.display = 'none';
           document.getElementById('reset-password').style.display = 'block';
       }
   });
});

let resetPassword = document.getElementById('reset-password');
resetPassword.addEventListener('click', () => {
    let resetP = new ResetPassword();
    let token = '9003887e79b9dc5092d2aa1e95e0c4ad62ce6bc6';

    resetP.resetPassword(window.localStorage.getItem('userIdChangesPassword'), token);
    document.getElementById('reset-password').style.display = 'none';
})

if (window.localStorage.getItem('userIdChangesPassword') !== null) {
    document.getElementById('reset-password-request').style.display = 'none';
    document.getElementById('reset-password').style.display = 'block';
}
