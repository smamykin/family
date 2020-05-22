import {Register} from './actions/Register';
import {Login} from "./actions/Login";
import {Products} from './actions/Products';
import {AddOffer} from './actions/Offer/AddOffer';
import {GetOffers} from "./actions/Offer/GetOffers";
import {DeleteOffer} from "./actions/Offer/DeleteOffer";

let login = new Login();
login.getJWTToken();
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
    let Offer = new AddOffer();
    Offer.addOffer(url.value, parseFloat(price.value), currency.value, product_id);
});

new GetOffers();
let deleteOffer = document.getElementById('delete-offer-button');
deleteOffer.addEventListener('click', () => {
    let offer = document.getElementById('delete-offer');
    let offer_id = offer.options[offer.selectedIndex].value;
    let deleteOffer = new DeleteOffer();
    deleteOffer.deleteOffer(offer_id);
});

