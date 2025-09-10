const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login')

registerBtn.addEventListener('click', () =>{
    container.classList.add("active");
});

loginBtn.addEventListener('click', () =>{
    container.classList.remove("active");
});

// Sosyal Medya İkonları İşlevselliği
document.addEventListener('DOMContentLoaded', function() {
    // Instagram
    const instagramIcons = document.querySelectorAll('.fa-instagram');
    instagramIcons.forEach(icon => {
        icon.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            window.open('https://www.instagram.com', '_blank');
        });
    });

    // X (Twitter)
    const xIcons = document.querySelectorAll('.fa-x-twitter');
    xIcons.forEach(icon => {
        icon.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            window.open('https://www.x.com', '_blank');
        });
    });

    // GitHub
    const githubIcons = document.querySelectorAll('.fa-github');
    githubIcons.forEach(icon => {
        icon.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            window.open('https://www.github.com', '_blank');
        });
    });

    // LinkedIn
    const linkedinIcons = document.querySelectorAll('.fa-linkedin-in');
    linkedinIcons.forEach(icon => {
        icon.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            window.open('https://www.linkedin.com', '_blank');
        });
    });
});

// CODED AND DESIGNED BY SPEXRON