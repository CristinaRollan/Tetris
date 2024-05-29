function submitForm(event) {
    event.preventDefault();
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var recaptchaResponse = grecaptcha.getResponse();

    if (!email && !password && !recaptchaResponse) {
      alert("Por favor, complete todos los campos.");
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
    if (!recaptchaResponse) {
      alert("Por favor, complete el reCAPTCHA.");
      return;
    }

    var formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('g-recaptcha-response', recaptchaResponse);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'usuario/procesar_index.php', true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        if (xhr.responseText.trim() === "OK") {
          window.location.href = "usuario/usuario.php";
        } else if (xhr.responseText.trim() === "account_blocked") {
          alert("Su cuenta está bloqueada. Por favor, contacte con soporte.");
        } else if (xhr.responseText.trim() === "account_not_validated") {
          alert("Su cuenta no ha sido validada. Por favor, revise su correo electrónico.");
        } else {
          alert("Usuario o contraseña incorrectos.");
        }
      } else {
        alert("Error al procesar la solicitud.");
      }
    };
    xhr.send(formData);
  }

  function handleCredentialResponse(response) {
    var formData = new FormData();
    formData.append('credential', response.credential);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'usuario/procesar_login_google.php', true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        if (xhr.responseText.trim() === "OK") {
          window.location.href = "usuario/usuario.php";
        } else if (xhr.responseText.trim() === "account_blocked") {
          alert("Su cuenta está bloqueada. Por favor, contacte con soporte.");
        } else if (xhr.responseText.trim() === "account_not_validated") {
          alert("Su cuenta no ha sido validada. Por favor, revise su correo electrónico.");
        } else {
          alert("Error al iniciar sesión con Google.");
        }
      } else {
        alert("Error al procesar la solicitud.");
      }
    };
    xhr.send(formData);
  }