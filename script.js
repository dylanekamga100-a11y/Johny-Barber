/* ============================================================
   SCRIPT.JS — Johnny Barber
   Fonctionnalités : Header dynamique, Validation Formulaire, 
   et Animations au scroll.
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. GESTION DU HEADER AU SCROLL
    // Ajoute une ombre et réduit la taille du header quand on scroll
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.style.padding = '10px 5%';
            header.style.backgroundColor = 'rgba(0, 0, 0, 0.95)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.5)';
        } else {
            header.style.padding = '20px 5%';
            header.style.backgroundColor = 'transparent';
            header.style.boxShadow = 'none';
        }
    });

    // 2. GESTION DU FORMULAIRE DE RÉSERVATION (Formulaire.html)
    const bookingForm = document.querySelector('.form-booking');
    
    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Empêche le rechargement de la page

            // Récupération des données (exemple)
            const name = bookingForm.querySelector('input[type="text"]').value;
            const date = bookingForm.querySelector('input[type="date"]').value;
            const service = bookingForm.querySelector('select').value;

            // Simulation d'envoi
            const submitBtn = bookingForm.querySelector('.btn-submit');
            const originalText = submitBtn.innerText;
            
            submitBtn.innerText = "Confirmation en cours...";
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.7";

            setTimeout(() => {
                alert(`Merci ${name} ! Votre rendez-vous pour un(e) "${service}" le ${date} est bien enregistré.`);
                bookingForm.reset();
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
            }, 1500);
        });
    }

    // 3. ANIMATION D'APPARITION (REVEAL)
    // Fait apparaître les éléments en douceur quand on défile
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // On cible les cartes de services et les sections du salon
    const animateElements = document.querySelectorAll('.service-item, .salon-intro, .map-container');
    
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.8s ease-out';
        observer.observe(el);
    });

    // 4. EFFET "ACTIVE" SUR LA NAVIGATION
    // Vérifie l'URL pour souligner le bon lien (si pas déjà fait en HTML)
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav a');

    navLinks.forEach(link => {
        if (currentPath.includes(link.getAttribute('href').replace('./', ''))) {
            link.classList.add('active');
        }
    });
});