let cart = JSON.parse(localStorage.getItem("cart")) || [];

// --- SEARCH LOGIC ---
function toggleSearch() {
    let searchContainer = document.getElementById("searchContainer");
    if (!searchContainer) return;
    searchContainer.style.display = (searchContainer.style.display === "block") ? "none" : "block";
    if (searchContainer.style.display === "block") {
        document.getElementById("searchInput").focus();
    }
}

function filterProducts() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let products = document.querySelectorAll('.product-box');
    products.forEach(product => {
        let title = product.querySelector('h3').innerText.toLowerCase();
        product.style.display = title.includes(input) ? "block" : "none";
    });
}

// --- CART SYSTEM ---
function addToCart(name, price, img) {
    cart.push({ name, price, img });
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCart(); // ড্রপডাউন আপডেট
    renderFullCart(); // যদি কার্ট পেজে থাকে তবে সেটিও আপডেট হবে
    showPopup(name + " added to cart 🛒");
}

function updateCart() {
    let cartItems = document.getElementById("cartItems");
    let cartCount = document.getElementById("cartCount");
    
    if (cartCount) cartCount.innerText = cart.length;
    if (!cartItems) return;

    cartItems.innerHTML = "";
    cart.forEach((item, index) => {
        let li = document.createElement("li");
        li.style.padding = "5px 0";
        li.style.borderBottom = "1px solid #eee";
        li.style.display = "flex";
        li.style.justifyContent = "space-between";
        li.innerHTML = `<span><strong>${item.name}</strong> - ৳${item.price}</span> 
                        <i class='bx bx-trash' style='cursor:pointer; color:red' onclick='removeFromCart(${index})'></i>`;
        cartItems.appendChild(li);
    });
}

// --- FULL CART PAGE LOGIC ---
function renderFullCart() {
    let fullCartBody = document.getElementById("fullCartBody");
    let totalPriceEl = document.getElementById("totalPrice");
    if (!fullCartBody) return;

    fullCartBody.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
        total += item.price;
        let tr = document.createElement("tr");
        tr.innerHTML = `
            <td style="padding:15px; border-bottom:1px solid #ddd;">
                <img src="${item.img}" style="width:50px; height:50px; object-fit:cover; margin-right:10px; vertical-align:middle;">
                ${item.name}
            </td>
            <td style="padding:15px; border-bottom:1px solid #ddd;">৳${item.price}</td>
            <td style="padding:15px; border-bottom:1px solid #ddd;">
                <button onclick="removeFromCart(${index})" style="background:red; color:white; border:none; padding:5px 10px; border-radius:30px; cursor:pointer;">Remove</button>
            </td>
        `;
        fullCartBody.appendChild(tr);
    });

    if (totalPriceEl) totalPriceEl.innerText = total;
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCart();
    renderFullCart();
}

function clearCart() {
    if (confirm("Are you sure you want to clear the cart?")) {
        cart = [];
        localStorage.setItem("cart", JSON.stringify(cart));
        updateCart();
        renderFullCart();
    }
}

function toggleCart() {
    let box = document.getElementById("cartDropdown");
    if (!box) return;
    box.style.display = (box.style.display === "block") ? "none" : "block";
}

// --- POPUP SYSTEM ---
function showPopup(message) {
    let popup = document.createElement("div");
    popup.className = "popup";
    popup.innerText = message;
    document.body.appendChild(popup);
    setTimeout(() => { popup.remove(); }, 2000);
}

window.onload = function() {
    updateCart();
    renderFullCart();
};