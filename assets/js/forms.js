// ================================================
//  JS FORMULAIRES — login.html & register.html
//  Connecte les formulaires HTML au backend PHP
// ================================================


// ════════════════════════════════════════
//  FORMULAIRE LOGIN
// ════════════════════════════════════════
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn     = document.getElementById('loginBtn');
        const errBox  = document.getElementById('loginError');
        const email   = document.getElementById('login_email').value.trim();
        const password = document.getElementById('login_password').value;

        // Reset erreur
        if (errBox) { errBox.style.display = 'none'; errBox.textContent = ''; }

        // Loading
        if (btn) { btn.disabled = true; btn.textContent = 'Connexion...'; }

        try {
            const formData = new FormData();
            formData.append('email',    email);
            formData.append('password', password);

            const res  = await fetch('pages/login.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                // Stocker aussi en sessionStorage (fallback JS)
                sessionStorage.setItem('femiFinger_user', JSON.stringify(data.user));
                window.location.href = data.redirect || '?page=dashboard';
            } else {
                showError(errBox, data.message || 'Erreur de connexion.');
                if (btn) { btn.disabled = false; btn.textContent = 'Se connecter'; }
            }
        } catch (err) {
            showError(errBox, 'Erreur réseau. Réessayez.');
            if (btn) { btn.disabled = false; btn.textContent = 'Se connecter'; }
        }
    });
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn    = document.getElementById('registerBtn');
        const errBox = document.getElementById('registerError');

        if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }
        if (btn)    { btn.disabled = true; btn.textContent = 'Inscription...'; }

        const formData = new FormData(registerForm);

        try {
            const res  = await fetch('pages/register.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                sessionStorage.setItem('femiFinger_user', JSON.stringify(data.user));
                window.location.href = data.redirect || '?page=dashboard';
            } else {
                // Afficher liste d'erreurs ou message unique
                const msg = data.errors
                    ? data.errors.map(e => `• ${e}`).join('<br>')
                    : (data.message || 'Erreur lors de l\'inscription.');
                showError(errBox, msg, true);
                if (btn) { btn.disabled = false; btn.textContent = 'Créer mon compte'; }
            }
        } catch (err) {
            showError(errBox, 'Erreur réseau. Réessayez.');
            if (btn) { btn.disabled = false; btn.textContent = 'Créer mon compte'; }
        }
    });
}


// ════════════════════════════════════════
//  UTILITAIRE — Afficher erreur
// ════════════════════════════════════════
function showError(box, msg, isHtml = false) {
    if (!box) { alert(msg); return; }
    if (isHtml) box.innerHTML = msg;
    else        box.textContent = msg;
    box.style.display = 'block';
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
