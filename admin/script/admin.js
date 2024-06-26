$(document).ready(function() {
    // Tab functionality
    $('.nav-link').on('click', function() {
        let tab = $(this).data('tab');
        $('.tab-content').removeClass('active');
        $('#' + tab).addClass('active');
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
    });

    // Fetch and display game stats
    if ($('#gameStatsChart').length) {
        $.get('estadistica_juego.php', function(data) {
            let labels = [];
            let gamesPlayed = [];

            data = JSON.parse(data);
            data.forEach(function(user) {
                labels.push(user.nombre_usuario);
                gamesPlayed.push(user.games_played);
            });

            let ctx = $('#gameStatsChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Partidas Jugadas',
                        data: gamesPlayed,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    }

    // Filter form submission
    $('#filter-form').on('submit', function(event) {
        event.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: 'filtrar_usuarios.php',
            type: 'GET',
            data: formData,
            success: function(response) {
                let usuarios = JSON.parse(response);
                let userTableBody = $('#user-table-body');
                userTableBody.empty();

                usuarios.forEach(function(usuario) {
                    let bloqueado = usuario.bloqueado ? 'Sí' : 'No';
                    let validado = usuario.validado ? 'No' : 'Sí'; // Validado 0 = Sí, 1 = No
                    let fechaRegistro = new Date(usuario.fecha_registro).toLocaleString('es-ES');

                    userTableBody.append(
                        '<tr>' +
                            '<td>' + usuario.id_usuario + '</td>' +
                            '<td>' + usuario.nombre_usuario + '</td>' +
                            '<td>' + usuario.email + '</td>' +
                            '<td>' + bloqueado + '</td>' +
                            '<td>' + validado + '</td>' +
                            '<td>' + fechaRegistro + '</td>' +
                            '<td>' +
                                '<form action="editar_usuario.php" method="post" style="display:inline;">' +
                                    '<input type="hidden" name="id_usuario" value="' + usuario.id_usuario + '">' +
                                    '<button type="submit" class="btn btn-sm btn-warning">Editar</button>' +
                                '</form>' +
                                '<form action="eliminar_usuario.php" method="post" style="display:inline;">' +
                                    '<input type="hidden" name="id_usuario" value="' + usuario.id_usuario + '">' +
                                    '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Estás seguro de que quieres eliminar este usuario?\');">Eliminar</button>' +
                                '</form>' +
                            '</td>' +
                        '</tr>'
                    );
                });
            }
        });
    });
});