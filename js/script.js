document.addEventListener("DOMContentLoaded", () => {
    fetch("php/products.php")
        .then(response => response.json())
        .then(data => {
            let productsSection = document.getElementById("products");
            productsSection.innerHTML = data.map(product => `
                <div>
                    <img src="${product.image}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <p>$${product.price}</p>
                </div>
            `).join("");
        });
});
