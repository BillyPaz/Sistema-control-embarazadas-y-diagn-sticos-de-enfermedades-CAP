

document.addEventListener("DOMContentLoaded", async function(){

    obtenerRoles();
   
})

function obtenerRoles(){
 $.ajax(
        {
            url:'../php/obtenerDatosRoles.php',
            method:'GET',
            dataType:'JSON',
            success: function(response){
                if(response.success){
                    console.log("roles",response.listRoles)
                    llenarCardRoles(response.listRoles)
                   document.getElementById("rolesCountText").textContent = response.countRoles.rol
                   document.getElementById("permisosCountText").textContent = response.countPermisos.permiso
                   console.log("permisos de usuario", response.permisosDetalle)
                    console.log("modulos de acceso", response.modulos)
                    
                    llenarSelectRoles(response.roles);
                    llenarSelectUsuario(response.listUsuarios)
                    llenarTablaUsuarioAsignacion(response.listUsuariosAsignaciones)
                }
              
            }
        }
    )
}

$(document).on('click', '.btnSaveRol', function () {
    const nombreRol = document.getElementById("nombreRol").value.trim();
    const descripcion = document.getElementById("descripcionRol").value.trim();

    const errorRol = document.getElementById("errorRol");
    errorRol.textContent = "";

    let valido = true;

    if (nombreRol === "") {
        errorRol.textContent = "El nombre del rol no puede estar vacío.";
        valido = false;
    }

    if (!valido) {
        return;
    }
    $.ajax({
        url: '../php/registrarRol.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            nombreRol: nombreRol,
            descripcion: descripcion
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Rol registrado correctamente.',
                    background: '#3a0ca3',
                    color: '#fff',
                    confirmButtonColor: '#4caf50',
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                }).then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo registrar el rol.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'Ocurrió un error al intentar registrar el rol.',
            });
        }
    });
});

function llenarSelectRoles(roles){
  const selectRol = document.getElementById("selectRol");
  selectRol.innerHTML = '<option value="">Seleccione una opción</option>';

  roles.forEach(rol=>{
    const option = document.createElement("option");
    option.value = rol.id_rol;
    option.textContent = rol.descripcion;

    selectRol.append(option);
  })
}

function llenarSelectUsuario(usuarios){
    const selectUsuario = document.getElementById("selectUsuario");

    selectUsuario.innerHTML ='<option value="">Seleccione un usuario</option>';
    usuarios.forEach(usuario=>{
        const option = document.createElement("option");
        option.value = usuario.id_usuario;
        option.textContent = usuario.nombre_usuario + ' ' + usuario.apellido_usuario;
        selectUsuario.append(option)
    })

}

function llenarCardRoles(roles){

    const cardRoles = $("#lista-roles");
    if(roles.length ===0){
       cardRoles.append(`
            <div class="alert alert-warning" role="alert">
                No hay roles disponibles.
            </div>
        `);
        return;
    }
    else{
        roles.forEach(rol=>{
            const card = `
            <a href="#" class="list-group-item list-group-item-action role-card"  onclick=cargarPermisosRol(${rol.id_rol}) data-role-id="${rol.id_rol}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">${rol.descripcion}</h5>
                                            <small class="text-muted">${rol.permisos} permisos</small>
                                        </div>
                                        <p class="mb-1">${rol.observaciones || 'Sin observaciones'}</p>
                                        <small>${rol.permisos} usuarios asignados</small>
                                    </a>
                                    `;
            cardRoles.append(card)
        })
    }


}
function listarPermisos(modulos) {
    const $contenedor = $('#contenedor-permisos');
    $contenedor.empty(); // Limpiar

    modulos.forEach(permiso=> {
            const permisoId = `permiso-${permiso.id_permiso}`;
            const $item = $(`
                <div class="permission-item">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               value="${permisoId}" 
                               id="${permisoId}" 
                                disabled>
                        <label class="form-check-label" for="${permisoId}">
                            ${permiso.descripcion}
                        </label>
                    </div>
                </div>
            `);

            $contenedor.append($item);
        });
    }

function cargarPermisosRol(idRol){
    $.ajax({
        url: '../php/getPermisos_por_rol.php',
        method: 'GET',
        data: { id_rol: idRol },
        dataType: 'json',
        success: function (response) {
        
            renderizarPermisos(response.modulos);
            document.getElementById("idRolAsignacion").value = idRol
        },
        error: function () {
            console.error('Error al cargar permisos del rol');
        }
    });
}

function renderizarPermisos(modulos) {
    const $contenedor = $('#contenedor-permisos');
    $contenedor.empty();
        modulos.forEach(permiso => {
            const permisoId = `permiso-${permiso.id_permiso}`;
            const checked = permiso.asignado ? 'checked' : '';
            
            const $item = $(`
                <div class="permission-item">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               value="${permiso.id_permiso}"
                              
                               name="permisos[]"
                                disabled
                               ${checked}>
                        <label class="form-check-label" for="${permisoId}">
                            ${permiso.permiso}
                        </label>
                    </div>
                </div>
                
            `);
           
            $contenedor.append($item);
           
        });

    // Botón para guardar
    const $boton = $(`
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary btnSavePermisos" disabled id="btn-guardar-permisos">
                <i class="bi bi-save"></i> Guardar Cambios
            </button>
        </div>
    `);

    $contenedor.append($boton);
}

$(document).on('click', '.btnSavePermisos', function(){
    const permisosSeleccionados = [];

    $('input[name="permisos[]"]:checked').each(function () {
        permisosSeleccionados.push($(this).val());
    });

    console.log("permisos seleccionados" + permisosSeleccionados)
    const idRol = document.getElementById("idRolAsignacion").value; 

    $.ajax(
        {

            url:'../php/actualizarRolesPermisos.php',
            method:'POST',
            dataType:'json',
            contentType: 'application/json',
            data:JSON.stringify({ 
                permisos: permisosSeleccionados,
                idRol : idRol
            
             }),
            success: function(response){
                if(response.success){
                    Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: 'Permisos registrados correctamente.',
    background: '#3a0ca3', // Fondo verde
    color: '#fff', // Texto blanco
    confirmButtonColor: '#4caf50', // Color del botón de confirmación
    timer: 2000,
    timerProgressBar: true,
    customClass: {
      
    popup: 'custom-swal-popup'
  },
}).then(() => {
    // Recargar página o cualquier acción posterior
    location.reload();
});
                }
            }
        }
    )

})

function asignarRol() {
    const idUsuario = document.getElementById("selectUsuario").value;
    const idRol = document.getElementById("selectRol").value;

    const errorUsuario = document.getElementById("errorSelectUsuario");
    const errorRol = document.getElementById("errorSelectRol");

    let esValido = true;

    // Limpiar errores anteriores
    errorUsuario.textContent = "";
    errorRol.textContent = "";

    // Validaciones
    if (!idUsuario) {
        errorUsuario.textContent = "Debe seleccionar un usuario.";
        esValido = false;
    }

    if (!idRol) {
        errorRol.textContent = "Debe seleccionar un rol.";
        esValido = false;
    }

    if (!esValido) {
        return;
    }

    // Si pasa la validación, hace la solicitud AJAX
    $.ajax({
        url: '../php/asignarRol.php',
        dataType: 'JSON',
        method: 'POST',
        data: {
            idUsuario: idUsuario,
            idRol: idRol
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Rol asignado',
                    text: 'Se asignó el rol correctamente',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                }).then(()=>{
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo asignar el rol',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Cerrar'
                }).then(()=>{
                    window.location.reload();
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'No se pudo completar la solicitud',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Cerrar'
            });
        }
    });
}
function llenarTablaUsuarioAsignacion(usuarios) {
    const tbody = document.querySelector("table tbody");
    tbody.innerHTML = ""; // Limpiar tabla antes de llenarla


    usuarios.forEach(usuario => {
        const tr = document.createElement("tr");

        // Nombre de usuario y nombre completo
        const tdUsuario = document.createElement("td");
        tdUsuario.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    ${usuario.nombre_usuario.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div class="fw-semibold">${usuario.nombre_usuario}</div>
                    
                </div>
            </div>
        `;

        // Rol actual
        const tdRol = document.createElement("td");
        const isSinRol = usuario.descripcion === "Sin rol";
        tdRol.innerHTML = `<span class="badge ${isSinRol ? 'bg-secondary' : 'bg-primary'}">${usuario.descripcion}</span>`;

        // Fecha de asignación
        const tdFecha = document.createElement("td");
        tdFecha.textContent = usuario.fecha_asignacion ? usuario.fecha_asignacion : "-";

        // Estado del usuario
        const tdEstado = document.createElement("td");
        const estadoTexto = usuario.estado_usuario == 1 ? "Activo" : "Inactivo";
        const estadoClase = usuario.estado_usuario == 1 ? "bg-success" : "bg-danger";
        tdEstado.innerHTML = `<span class="badge ${estadoClase}">${estadoTexto}</span>`;

        // Acciones
        const tdAcciones = document.createElement("td");
        tdAcciones.innerHTML = `
     <button 
    class="btn btn-outline-primary btn-sm" 
    data-bs-toggle="modal" 
    data-bs-target="#modalEditarRol" 
    onclick='editarUsuarioRol(${usuario.id_usuario})'>
    <i class="bi bi-pencil"></i> Editar
</button>

        `;

        tr.appendChild(tdUsuario);
        tr.appendChild(tdRol);
        tr.appendChild(tdFecha);
        tr.appendChild(tdEstado);
        tr.appendChild(tdAcciones);

        tbody.appendChild(tr);
    });
}

//mostrar los permisos al elegir el rol y una vista previa sobrelos permisos asignados del rol

document.getElementById("selectRol").addEventListener('change', function () {
    const idRol = this.value;

    $.ajax({
        url: '../php/obtenerPermisosPorRol.php',
        type: 'GET',
        dataType: 'JSON',
        data: { idRol: idRol },
        success: function (response) {
            const preview = document.getElementById("permisos-preview");

            if (response.success) {
                console.log(response.permisos)
                if (!response.permisos || response.permisos.length === 0) {
                    preview.innerHTML = "Rol sin permiso asignado";
                } else {
                    const listItems = response.permisos.map(permiso => `<li>${permiso}</li>`).join('');
                    preview.innerHTML = `<ul class="mb-0">${listItems}</ul>`;
                }
            } else {
                preview.innerHTML = "Error: No se pudo obtener permisos.";
            }
        },
        error: function () {
            document.getElementById("permisos-preview").innerHTML = "Error en la solicitud AJAX.";
        }
    });
});

function editarUsuarioRol(idUsuario){
    $.ajax({
        url:'../php/obtenerDatoRolEdit.php',
        dataType:'JSON',
        method:'GET',
        data:{idUsuario : idUsuario},
        success: function(response){
            if(response.success){
                console.log(response);
                document.getElementById("editIdUsuario").value = response.datosUsuarioEdit.id_usuario
                document.getElementById("editNombreUsuario").textContent= response.datosUsuarioEdit.nombre_usuario + " " + response.datosUsuarioEdit.apellido_usuario
                llenarSelectRolEdit(response.roles, response.datosUsuarioEdit.id_rol)
            }
        }
    })
    
}

function llenarSelectRolEdit(roles, idRolUsuario) {
    const selectRolEdit = document.getElementById("editRol");
    selectRolEdit.innerHTML = ''; // Limpiar antes de llenar

    // Opción por defecto
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "Seleccione un rol";
    defaultOption.textContent = "— Sin rol asignado —";
    selectRolEdit.appendChild(defaultOption);

    roles.forEach(rol => {
        const option = document.createElement("option");
        option.value = rol.id_rol;
        option.textContent = rol.descripcion;

        // Si es el rol asignado actualmente, marcar como seleccionado
        if (rol.id_rol == idRolUsuario) {
            option.selected = true;
        }

        
        selectRolEdit.appendChild(option);
    });
}


    function guardarRolEditado() {
    const idUsuario = document.getElementById("editIdUsuario").value;
    const idRol = document.getElementById("editRol").value;
    const errorRol = document.getElementById("errorEditRol");

    errorRol.textContent = "";

    // Si está vacío (sin rol seleccionado), eliminar asignación
    if (idRol === "") {
        // Confirmación opcional
        Swal.fire({
            title: '¿Eliminar rol?',
            text: '¿Estás seguro de eliminar la asignación de rol para este usuario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../php/eliminarRolUsuario.php',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { idUsuario },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rol eliminado',
                                text: 'El usuario ya no tiene rol asignado.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(()=>{
                    window.location.reload();
                });;

                            // Cerrar modal y recargar tabla
                            const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditarRol"));
                            modal.hide();
                            cargarUsuariosAsignaciones(); // O la función que actualice la tabla
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo eliminar el rol.'
                            });
                        }
                    }
                });
            }
        });

        return; // Salir de la función aquí
    }

    // Si tiene rol seleccionado, se actualiza normalmente
    $.ajax({
        url: '../php/actualizarRolUsuario.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            idUsuario: idUsuario,
            idRol: idRol
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Rol actualizado!',
                    text: 'El rol del usuario se actualizó correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(()=>{
                    window.location.reload();
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditarRol"));
                modal.hide();
                cargarUsuariosAsignaciones();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el rol.'
                });
            }
        }
    });
}
