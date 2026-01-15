// script.js

// Mobile menu toggle
const menuToggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");

if (menuToggle && navLinks) {
    menuToggle.addEventListener("click", () => {
        navLinks.classList.toggle("show");
    });
}

// Smooth scroll and active link highlight
const links = document.querySelectorAll(".nav-links a");

links.forEach(link => {
    link.addEventListener("click", function (e) {
        const targetId = this.getAttribute("href");
        if (targetId.startsWith("#")) {
            e.preventDefault();
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: "smooth"
                });
            }
            navLinks.classList.remove("show");
        }
    });
});

// Simple front-end validation (extra layer)
const contactForm = document.getElementById("contactForm");
if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const subject = document.getElementById("subject").value.trim();
        const message = document.getElementById("message").value.trim();

        if (!name || !email || !subject || !message) {
            alert("Please fill all required fields.");
            e.preventDefault();
            return;
        }

        const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email.");
            e.preventDefault();
        }
    });
}
// Theme toggle (dark/light)
const themeToggle = document.getElementById("themeToggle");

// Apply saved theme on load
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "light") {
    document.body.classList.add("light-mode");
    if (themeToggle) themeToggle.textContent = "☀️";
}

if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("light-mode");
        const isLight = document.body.classList.contains("light-mode");
        localStorage.setItem("theme", isLight ? "light" : "dark");
        themeToggle.textContent = isLight ? "☀️" : "🌙";
    });
}
// Project search filter
const projectSearch = document.getElementById("projectSearch");
const projectCards = document.querySelectorAll("#projectsGrid .project-card");

if (projectSearch && projectCards.length > 0) {
    projectSearch.addEventListener("input", function () {
        const q = this.value.trim().toLowerCase();

        projectCards.forEach(card => {
            const title = card.getAttribute("data-title") || "";
            const tech = card.getAttribute("data-tech") || "";
            if (title.includes(q) || tech.includes(q)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    });
}


