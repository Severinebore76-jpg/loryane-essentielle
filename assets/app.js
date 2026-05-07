import './stimulus_bootstrap.js';

function initApp() {

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

            fetch(url)
                .then(response => {

                    if (!response.ok) {
                        throw new Error("Erreur ajout panier");
                    }

                    const container = this.closest(".product-card, .product-detail");

                    const popup = container?.querySelector(".add-popup");

                    if (popup) {

                        popup.classList.add("show");

                        setTimeout(() => {
                            popup.classList.remove("show");
                        }, 2000);

                    }

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

}

document.addEventListener("DOMContentLoaded", initApp);
document.addEventListener("turbo:load", initApp);

console.log('Application JavaScript loaded.');
