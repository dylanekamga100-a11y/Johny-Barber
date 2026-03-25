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
            // ON NE FAIT PLUS e.preventDefault() ici pour laisser le PHP travailler.
            
            const submitBtn = bookingForm.querySelector('.btn-submit');
            const originalText = submitBtn.innerText;
        
            // On donne un feedback visuel avant que la page ne change
            submitBtn.innerText = "Envoi en cours...";
            submitBtn.style.opacity = "0.7";
            
            // Optionnel : On peut ajouter une petite sécurité pour éviter les doubles clics
            setTimeout(() => {
                submitBtn.disabled = true;
            }, 1);
        });
    }

    const dateInput = document.querySelector('input.date-time[type="date"]');

    // 1. Gérer la période de 2 semaines (Min et Max)
    const aujourdhui = new Date();
    const dansDeuxSemaines = new Date();
    dansDeuxSemaines.setDate(aujourdhui.getDate() + 14);

    // Formater la date en YYYY-MM-DD pour l'attribut HTML
    const formaterDate = (date) => date.toISOString().split('T')[0];

    dateInput.min = formaterDate(aujourdhui);
    dateInput.max = formaterDate(dansDeuxSemaines);

    // 2. Bloquer le dimanche
    dateInput.addEventListener('input', function () {
        const dateSelectionnee = new Date(this.value);
        const jour = dateSelectionnee.getUTCDay(); // 0 = Dimanche, 1 = Lundi...

        if (jour === 0) {
            alert("Le salon est fermé le dimanche. Merci de choisir un autre jour.");
            this.value = ""; // Réinitialise le champ
        }
    });

    const timeInput = document.querySelector('input.date-time[type="time"]');

    timeInput.addEventListener('input', function () {
        const time = this.value;
        if (time < "09:00" || time > "20:30") {
            alert("Désolé, le salon est ouvert de 09h00 à 20h30.");
            this.value = ""; // Réinitialise le champ si l'heure est invalide
        }
    });

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
