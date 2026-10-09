(function () {
    "use strict";

    const form = document.getElementById("filter-form");
    if (form) {
        function debounce(fn, ms) {
            let timer;
            return () => {
                clearTimeout(timer);
                timer = setTimeout(fn, ms);
            };
        }

        function showLoading() {
            const loader = document.getElementById("table-loader");
            if (loader) loader.classList.replace("hidden", "flex");
        }

        const submit = () => {
            showLoading();
            form.submit();
        };

        form.querySelectorAll('input[type="text"], input[type="search"]').forEach((el) => {
            el.addEventListener("input", debounce(submit, 500));
        });

        form.querySelectorAll('input[type="date"], input[type="checkbox"], select').forEach((el) => {
            el.addEventListener("change", submit);
        });
    }

    // Cancel-registration modal
    const modal = document.getElementById("cancel-modal");
    const modalForm = document.getElementById("cancel-form");

    if (modal && modalForm) {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".cancel-btn");
            if (!btn) return;
            modalForm.action = btn.dataset.url;
            document.getElementById("cancel-attendee").textContent = btn.dataset.name;
            modal.classList.replace("hidden", "flex");
            modalForm.querySelector("textarea")?.focus();
        });

        function closeModal() {
            modal.classList.replace("flex", "hidden");
        }

        document.getElementById("cancel-close")?.addEventListener("click", closeModal);
        modal.addEventListener("click", (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    }
})();
