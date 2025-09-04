//////////////// cerrar sesion despues de 5 minutos de inactividad    ////////////////////
var timeout;
var timeoutLimit = 40 * 60 * 1000; 

// Función para reiniciar el temporizador
function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(function() {
        window.location.href = "logout.php"; // Redirige a logout después de 5 minutos
    }, timeoutLimit);
}

// Detectar actividad del usuario
window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeydown = resetTimer;

////////////////////////////////// fin cerrar sesion   ///////////////////////


/////////////////////////////////    Formatos para tabla //////////////////////
$(document).ready(function () {
    $('#tablaComun').DataTable({
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true
    });
});
///////////////////////////////  Fin formatos de tabla /////////////////////////////


///////////////////////////////   funcion para buscar al personal con la curp en licenciafrom.php 

document.addEventListener('DOMContentLoaded', () => {
    const btnValidar = document.getElementById('validarRFC');

    if (btnValidar) {
        btnValidar.addEventListener('click', () => {
            const rfcInput = document.getElementById('rfc');
            const rfc = rfcInput.value.trim();

            if (!rfc) {
                alert("Por favor ingresa una RFC.");
                return;
            }

           fetch(`buscarpersonal.php?rfc=${encodeURIComponent(rfc)}`)
                .then(response => response.text())
                .then(text => {
                    console.log("Respuesta cruda del servidor:", text);
                    try {
                        const data = JSON.parse(text);
                        if (data && data.id) {
                            document.getElementById('id_personal').value = data.id;
                            document.getElementById('curp').value = data.curp;
                            document.getElementById('nombre').value = data.nombre;
                            document.getElementById('centro').value = data.centro;
                            document.getElementById('jurisdiccion').value = data.jurisdiccion;
                        } else {
                            alert("No se encontró personal con esa RFC");
                        }
                    } catch (e) {
                        console.error("Error al convertir JSON:", e);
                        alert("La respuesta no fue válida. Verifica que el archivo PHP devuelva JSON.");
                    }
                })
                .catch(error => {
                    console.error('Error al validar RFC:', error);
            });

        });
    }
});

//                                         fin buscar curp


//////////////////////////////////////  Fintrar centros por juris en personalform.php
document.getElementById('adscripcion').addEventListener('change', function () {
    const adscripId = this.value;
    const centroSelect = document.getElementById('centro');

    // Limpiar las opciones actuales
    centroSelect.innerHTML = '<option value="">Cargando centros...</option>';

    fetch(`personalform.php?adscrip_id=${adscripId}`)
        .then(response => response.json())
        .then(data => {
            centroSelect.innerHTML = '<option value="">Seleccione un centro</option>';
            data.forEach(centro => {
                const option = document.createElement('option');
                option.value = centro.id;
                option.textContent = centro.nombre;
                centroSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar centros:', error);
            centroSelect.innerHTML = '<option value="">Error al cargar centros</option>';
        });
});
//                                    fin filtrar centros

//                                    mostrar sueldo bruto o sueldo neto en personal form
function toggleSueldoFields() {
    var tipoSueldo = document.getElementById('tipo_sueldo').value;
    
    // Ocultamos ambos campos
    document.getElementById('sueldo_neto_field').style.display = 'none';
    document.getElementById('sueldo_bruto_field').style.display = 'none';

    // Mostramos solo el campo correspondiente
    if (tipoSueldo === 'neto') {
        document.getElementById('sueldo_neto_field').style.display = 'block';
    } else if (tipoSueldo === 'bruto') {
        document.getElementById('sueldo_bruto_field').style.display = 'block';
    }
}

// Validar que solo un sueldo sea ingresado
document.querySelector('form').addEventListener('submit', function(event) {
    let sueldoNeto = document.getElementById('sueldo_neto').value;
    let sueldoBruto = document.getElementById('sueldo_bruto').value;

    if ((sueldoNeto && sueldoBruto) || (!sueldoNeto && !sueldoBruto)) {
        event.preventDefault();
        document.getElementById('error-message').style.display = 'block';
        return false;
    }

    return true;
});

//                          fin mostrar sueldo bruto o neto
