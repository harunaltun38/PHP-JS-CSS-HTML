function purchaseClicked() {
    "use strict";
    alert('Danke für ihre Bestellung');
    var cartItems = document.getElementsByClassName('cart-items')[0];
    while (cartItems.hasChildNodes()) {
        cartItems.removeChild(cartItems.firstChild);
    }
    updateCartTotal();
}

function removeCartItem(dom) {
    "use strict";
    var row = dom.parentElement.parentElement;
    row.parentElement.removeChild(row);
    updateCartTotal();
    window.scrollBy(0, -1);
}

function quantityChanged(input) {
    "use strict";
    if (isNaN(input.value) || input.value <= 0) {
        input.value = 1;
    }
    if (input.value > 20) {
        input.value = 20;
    }
    updateCartTotal();
}

function addToCartClicked(shopItemImg) {
    "use strict";
    var name = shopItemImg.getAttribute("title");
    var price = shopItemImg.getAttribute("data-price");
    var imageSrc = shopItemImg.getAttribute("src");
    
    addItemToCart(name, price, imageSrc);
    updateCartTotal();
}

function addItemToCart(name, price, imageSrc) {
    "use strict";
    var cartItems = document.getElementsByClassName('cart-items')[0];
    var cartItemNames = cartItems.getElementsByClassName('cart-item-name');
    for (var i = 0; i < cartItemNames.length; i++) {
        if (cartItemNames[i].innerText == name) {
            var quantityEl = cartItemNames[i].parentElement.parentElement.getElementsByClassName('cart-quantity-input')[0];
            var aktquant = Number.parseInt(quantityEl.value);
            quantityEl.value = (aktquant + 1);
            if (quantityEl.value > 20) {
                quantityEl.value = 20;
            }
            return;
        }
    }
    var cartRow = document.createElement('div');
    cartRow.classList.add('cart-row');
    
    var div1 = document.createElement('div');
    div1.setAttribute("class", "cart-item cart-column");
    var img = document.createElement('img');
    img.setAttribute("class", "cart-item-image");
    img.setAttribute("src", imageSrc);
    img.setAttribute("width", "100");
    img.setAttribute("height", "100");
    var span1 = document.createElement('span');
    span1.setAttribute("class", "cart-item-name");
    var textnode1 = document.createTextNode(name);
    span1.appendChild(textnode1);
    var hiddeninput1 = document.createElement('input');
    hiddeninput1.setAttribute("type", "hidden");
    hiddeninput1.setAttribute("name", "speisen[]");
    hiddeninput1.setAttribute("value", name);
    div1.appendChild(img);
    div1.appendChild(span1);
    div1.appendChild(hiddeninput1);
    
    var span2 = document.createElement('span');
    span2.setAttribute("class", "cart-price cart-column");
    var textnode2 = document.createTextNode("$" + price);
    span2.appendChild(textnode2);
    
    var div2 = document.createElement('div');
    div2.setAttribute("class", "cart-quantity cart-column");
    var input = document.createElement('input');
    input.setAttribute("class", "cart-quantity-input");
    input.setAttribute("type", "number");
    input.setAttribute("name", "anzahl[]");
    input.setAttribute("value", "1");
    input.setAttribute("onchange", "quantityChanged(this)")
    var button = document.createElement('button');
    button.setAttribute("class", "btn btn-danger");
    button.setAttribute("type", "button");
    button.setAttribute("onclick", "removeCartItem(this)")
    var textnode3 = document.createTextNode("REMOVE");
    button.appendChild(textnode3);
    div2.appendChild(input);
    div2.appendChild(button);
    
    cartRow.appendChild(div1);
    cartRow.appendChild(span2);
    cartRow.appendChild(div2);
        
/*    var cartRowContents = `
        <div class="cart-item cart-column" name="test" value="1">
            <img class="cart-item-image" src="${imageSrc}" width="100" height="100">
            <span class="cart-item-name">${name}</span>
        </div>
        <span class="cart-price cart-column">$${price}</span>
        <div class="cart-quantity cart-column">
            <input class="cart-quantity-input" type="number" value="1">
            <button class="btn btn-danger" type="button">REMOVE</button>
        </div>`;
    cartRow.innerHTML = cartRowContents;*/
    cartItems.append(cartRow);
    //cartRow.getElementsByClassName('btn-danger')[0].addEventListener('click', removeCartItem);
    //cartRow.getElementsByClassName('cart-quantity-input')[0].addEventListener('change', quantityChanged);
}

function updateCartTotal() {
    "use strict";
    var cartItemContainer = document.getElementsByClassName('cart-items')[0];
    var cartRows = cartItemContainer.getElementsByClassName('cart-row');
    var total = 0;
    for (var i = 0; i < cartRows.length; i++) {
        var cartRow = cartRows[i];
        var priceElement = cartRow.getElementsByClassName('cart-price')[0];
        var quantityElement = cartRow.getElementsByClassName('cart-quantity-input')[0];
        var price = parseFloat(priceElement.innerText.replace('$', ''));
        var quantity = quantityElement.value;
        total = total + (price * quantity);
    }
    total = Math.round(total * 100) / 100;
    document.getElementsByClassName('cart-total-price')[0].innerText = '$' + total;
    checkValidation();
}

function checkValidation() {
    "use strict";
    var form = document.getElementsByTagName("form")[0];
    var submitbtn = document.getElementsByClassName('btn-purchase')[0];
    var warenkorbelems = document.getElementsByClassName('cart-items')[0].childElementCount;
    if (form.checkValidity() == true && warenkorbelems > 0) {
        submitbtn.disabled = false;
    } else {
        submitbtn.disabled = true;
    }
}