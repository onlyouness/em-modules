let popup = document.querySelector('#popup_cart');
let closeBtn = document.querySelector('#close_pop_cart');
closeBtn.addEventListener('click', function () {
    popup.classList.remove('active');
})
function displayPopup() {
    let price = getPrice();
    if (price < popcartprice) {
        popup.classList.add('active')
    }
}
displayPopup()

function getPrice() {
    let price = prestashop.cart.totals.total_including_tax.amount;
    return price
}

prestashop.on("updatedCart", function () {
    displayPopup()
});