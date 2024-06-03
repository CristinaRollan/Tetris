function submitForm() {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var recaptchaResponse = grecaptcha.getResponse();

    if (!email && !password && !recaptchaResponse) {
            alert("Por favor, complete todos los campos.");
            return;
    }
    if (!recaptchaResponse) {
      alert("Por favor, complete el reCAPTCHA.");
      return;
    }
    if (!email) {
            alert("Por favor, ingresa un email.");
            return;
    }
    if (!password) {
            alert("Por favor, ingresa una contraseña.");
            return;
    }
    var formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('g-recaptcha-response', recaptchaResponse);

    // Crear una solicitud AJAX para enviar los datos al PHP
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'procesar_admin.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "OK") {
              window.location.href = "panel_admin.php";
            } else {
              alert("Usuario o contraseña incorrectos.")
            }
        }
    };
    xhr.send(formData);
}