import {Products} from './actions/Products';
import {AddOffer} from './actions/Offer/AddOffer';

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

