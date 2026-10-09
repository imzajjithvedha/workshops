// Submit button loading state
document.addEventListener("submit", (e) => {
    if (e.defaultPrevented) return;
    const button = e.target.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.classList.add("submit-button-loading");
    }
});

// Password visibility toggle
document.addEventListener("click", (e) => {
    const icon = e.target.closest(".toggle-password");
    if (!icon) return;

    const input = icon.closest(".relative").querySelector("input");
    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
    }
});

// Mobile navbar menu
document.addEventListener("click", (e) => {
    if (!e.target.closest("#mobile-menu-toggle")) return;
    const menu = document.getElementById("mobile-menu");
    const icon = document.getElementById("menu-icon");
    menu?.classList.toggle("hidden");

    if (menu?.classList.contains("hidden")) {
        icon?.classList.replace("bi-x", "bi-list");
    } else {
        icon?.classList.replace("bi-list", "bi-x");
    }
});

// Sidebar toggle (small screens)
document.addEventListener("click", (e) => {
    if (!e.target.closest("#sidebarToggle")) return;
    const sidebar = document.getElementById("sidebar");
    sidebar?.classList.toggle("hidden");
    sidebar?.classList.toggle("flex");
});
