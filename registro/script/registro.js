function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(email);
}

function validateDomain(email) {
    var validDomains = ["gmail.com", "yahoo.com", "yahoo.es", "hotmail.com", "hotmail.es", "outlook.com", "educa.madrid.org"];
    var domain = email.split('@')[1];
    return validDomains.includes(domain);
}

function submitForm(event) {
    event.preventDefault();
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password1").value;
    var confirmPassword = document.getElementById("password2").value;
    var privacy = document.getElementById("privacy").checked;
    var recaptchaResponse = grecaptcha.getResponse();

    if (!username && !email && !password && !confirmPassword && !privacy) {
        alert("Por favor, complete todos los campos y acepte la política de protección de datos.");
        return;
    }

    if (username.length > 30) {
        alert("El nombre de usuario no puede tener más de 30 caracteres.");
        return;
    }

    if (email.length > 50) {
        alert("El email no puede tener más de 50 caracteres.");
        return;
    }

    if (password.length > 50) {
        alert("La contraseña no puede tener más de 50 caracteres.");
        return;
    }

    if (!validateEmail(email)) {
        alert("Por favor, introduzca un email válido.");
        return;
    }

    if (!validateDomain(email)) {
        alert("Por favor, introduzca un email con un dominio válido.");
        return;
    }

    if (password !== confirmPassword) {
        alert("Las contraseñas no coinciden.");
        return;
    }

    if (!recaptchaResponse) {
        alert("Por favor, complete el reCAPTCHA.");
        return;
    }

    var formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password1', password);
    formData.append('password2', confirmPassword);
    formData.append('g-recaptcha-response', recaptchaResponse);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'procesar_ingreso.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = xhr.responseText.trim();
                if (response === "OK") {
                    alert("Revisa tu correo electrónico para confirmar cuenta");
                    window.location.href = "../index.php";
                } else if (response === "email_exists") {
                    alert("Usuario ya registrado");
                } else if (response === "account_blocked") {
                    alert("Su cuenta está bloqueada. Por favor, contacte con soporte.");
                } else if (response === "account_not_validated") {
                    alert("Su cuenta no ha sido validada. Por favor, revise su correo electrónico.");
                } else {
                    alert("Error: " + response);
                }
        } else {
            alert("Error al procesar la solicitud.");
        }
    };
    xhr.send(formData);
}