import './stimulus_bootstrap.js';
document.addEventListener("DOMContentLoaded", function () {
    function toggle(btnId, panelId) {
        const btn = /** @type {HTMLElement} */ (
            document.getElementById(btnId)
        );
        const panel = /** @type {HTMLElement} */ (
            document.getElementById(panelId)
        );
        if (!btn || !panel) return;
        btn.addEventListener("click", () => {
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
    // MENU MOBILE
    toggle("menu-toggle", "menu-panel");
    // FILTRES
    toggle("filter-toggle", "filter-panel");

    // AJOUT PANIER + POPUP
    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const url = this.dataset.url;
            const card = this.closest(".product-card");
            if (!card) return;
            const popup = card.querySelector(".add-popup");
            if (!popup) return;
            fetch(url)
                .then(() => {
                    popup.classList.add("show");
                    setTimeout(() => {
                        popup.classList.remove("show");
                    }, 2000);
                })
                .catch(error => {
                    console.error("Erreur ajout panier :", error);
                });
        });
    });
    // IMAGE FALLBACK
    document.querySelectorAll(".product-image").forEach(image => {
        image.addEventListener("error", function () {
            this.src = this.dataset.fallback;
        });
    });
});
console.log('Application JavaScript loaded.');
