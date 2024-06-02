document.getElementById('submit').addEventListener('click', function() {
    var email = document.getElementById('email').value;

    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!re.test(email)) {
        alert('¡Por favor, introduce un correo electrónico válido!');
        return;
    }

    var formData = new FormData();
    formData.append('email', email);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            alert('Se ha enviado el enlace para restablecer la contraseña a su correo electrónico.');
        } else if (data.trim() === "account_blocked") {
            alert('Su cuenta está bloqueada. Por favor, contacte con soporte.');
        } else if (data.trim() === "account_not_validated") {
            alert('Su cuenta no ha sido validada. Por favor, revise su correo electrónico.');
        } else if (data.trim() === "NoAccountError") {
            alert('Lo siento, no hay ninguna cuenta asociada con este correo electrónico.');
        } else {
            alert(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error. Inténtalo de nuevo.');
    });
});