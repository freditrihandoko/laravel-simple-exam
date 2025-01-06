import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    var themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
    var themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

    function updateThemeToggleIcons() {
        if (document.documentElement.classList.contains("dark")) {
            themeToggleDarkIcon.classList.add("hidden");
            themeToggleLightIcon.classList.remove("hidden");
        } else {
            themeToggleDarkIcon.classList.remove("hidden");
            themeToggleLightIcon.classList.add("hidden");
        }
    }

    function applyThemePreference() {
        if (
            localStorage.getItem("color-theme") === "dark" ||
            (!("color-theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
        } else {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
        }
        updateThemeToggleIcons();
    }

    // Apply theme preference on page load
    applyThemePreference();

    // Toggle theme on button click
    var themeToggleBtn = document.getElementById("theme-toggle");
    themeToggleBtn.addEventListener("click", function () {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
        } else {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
        }
        updateThemeToggleIcons();
    });

    // Listen to system theme changes
    window.matchMedia("(prefers-color-scheme: dark)").addListener((e) => {
        if (e.matches) {
            if (localStorage.getItem("color-theme") !== "light") {
                document.documentElement.classList.add("dark");
            }
        } else {
            if (localStorage.getItem("color-theme") === "dark") {
                document.documentElement.classList.remove("dark");
            }
        }
        updateThemeToggleIcons();
    });
});