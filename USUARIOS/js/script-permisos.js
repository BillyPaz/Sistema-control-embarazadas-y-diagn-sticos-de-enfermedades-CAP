

document.addEventListener("DOMContentLoaded", async function(){


    $.ajax({
        url:'../php/obtenerDatosPermisos.php',
        method:'GET',
        dataType:'JSON',
        success: function(response){
            if(response.success){
                console.log("permisos detalle", response.modulos)
                console.log("modulos"+response.listModulos);
              
                document.getElementById("totalPermisosText").textContent = response.totalPermisos.total_permisos
                document.getElementById("permisoActivoText").textContent =response.totalPermisos.permisos_activos
                document.getElementById("permisoInactivoText").textContent =response.totalPermisos.permisos_inactivos
                
                llenarTablaPermiso(response.listPermisos)
               console.log("permisos de acceso", response.permisosDetalle)
     

            }
        }
    })
});

function llenarTablaPermiso(permisos) {
 
    const $tabla = $('#permisoTable');
    const $tbody = $tabla.find('tbody');

    $tbody.empty();
 
    if (permisos.length === 0) {

        const fila = `
          <tr>
            <td colspan="6" style="text-align: center;">No hay ningún permiso registrado</td>
          </tr>
        `;
        $tbody.append(fila);
    } else {
        permisos.forEach(item => {
          const activo = item.activo == 1 ? 'Activo' : 'Inactivo';
          const colorEstado = item.activo==1 ?'badge-active' :'badge-inactive';
            const fila = `
               <tr>
                                       <td><span class="">${item.id_permiso || 'Sin rol'}</span></td>
                                        <td><span class="">${item.descripcion || 'Sin rol'}</span></td>
                                        <td><span class="">${item.observaciones || 'Sin observaciones'}</span></td>
                                        <td><span class="badge badge-status ${colorEstado}">${activo}</span></td>
                                        
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btnEditarPermiso" data-bs-toggle="modal" data-bs-target="#modalEditarPermiso" data-id="${item.id_permiso}" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                           
                                        </td>
                                    </tr>
            `;
            $tbody.append(fila);
        });
        // Lista de colores aleatorios
const colores = ['#F94144', '#F3722C', '#F8961E', '#43AA8B', '#577590', '#277DA1'];

document.querySelectorAll('.inicialText').forEach(avatar => {
    const colorAleatorio = colores[Math.floor(Math.random() * colores.length)];
    avatar.style.background = colorAleatorio;
    avatar.style.color = '#fff';
    avatar.style.borderRadius = '50%';
    avatar.style.width = '40px';
    avatar.style.height = '40px';
    avatar.style.display = 'flex';
    avatar.style.alignItems = 'center';
    avatar.style.justifyContent = 'center';
    avatar.style.fontWeight = 'bold';
});

        // Inicializar DataTable después de llenar el contenido
    $tabla.DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
    }

    
}





$(document).on('click', '.btnSavePermiso', function () {
    const nombre = document.getElementById("permisoNombre").value.trim();
    const descripcion = document.getElementById("permisoDescripcion").value.trim();
    

    const errorNombre = document.getElementById("errorNombrePermiso");


    // Limpiar errores previos
    errorNombre.textContent = "";
  
    let esValido = true;

    if (nombre === "") {
        errorNombre.textContent = "El nombre del permiso es obligatorio.";
        esValido = false;
    }

  
    if (!esValido) {
        return; // Detener si hay errores
    }

    // Si todo es válido, continuar con el registro
    $.ajax({
        url: '../php/registrarPermiso.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            nombre: nombre,
            descripcion: descripcion
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Permiso registrado correctamente.',
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
                    text: response.message || 'No se pudo registrar el permiso.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'No se pudo completar la solicitud.',
            });
        }
    });
});



$(document).on('click', '.btnEditPermiso', function(){
    const idPermiso = $(this).data('id');
    console.log("permiso"+ idPermiso)
    $.ajax({
        url:'../php/obtenerPermisoEdit.php',
        method:'GET',
        dataType:'JSON',
        data:{
            idPermiso : idPermiso
        },
        success: function(response){
            if(response.success){
                console.log(response.permiso)
                document.getElementById("id_permisoEdit").value = idPermiso
                document.getElementById("nombrePermisoEdit").value = response.permiso.nombre
                document.getElementById("clavePermisoEdit").value = response.permiso.clave
                document.getElementById("estadoPermisoEdit").checked = response.permiso.activo
                document.getElementById("descripcionPermisoEdit").value = response.permiso.descripcion
            }
        }
    })
})


$(document).on('click', '.btnActualizarPermiso', function () {
    const idPermiso = document.getElementById("id_permisoEdit").value;
    const nombrePermiso = document.getElementById("nombrePermisoEdit").value.trim();
    
    const estado = document.getElementById("estadoPermisoEdit").checked ? 1 : 0;
    const descripcion = document.getElementById("descripcionPermisoEdit").value.trim();


    // Elementos para mostrar errores
    const errorNombre = document.getElementById("errorNombrePermisoEdit");

    // Limpiar errores previos
    errorNombre.textContent = "";

    let valido = true;

    // Validaciones
    if (nombrePermiso === "") {
        errorNombre.textContent = "El nombre del permiso no puede estar vacío.";
        valido = false;
    }


    // Si no pasa validaciones, detener
    if (!valido) {
        return;
    }

    // Si todo está bien, enviar AJAX
    $.ajax({
        url: '../php/actualizarPermiso.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            idPermiso: idPermiso,
            nombrePermiso: nombrePermiso,
            estado: estado,
            descripcion: descripcion,
          
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Permiso actualizado correctamente.',
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
                    text: response.message || 'No se pudo actualizar el permiso.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'Ocurrió un error al intentar actualizar el permiso.',
            });
        }
    });
});

$('#modalEditarPermiso').on('hidden.bs.modal', function () {
    // Limpiar campos de texto
    document.getElementById("nombrePermisoEdit").value = "";
    document.getElementById("clavePermisoEdit").value = "";
    document.getElementById("descripcionPermisoEdit").value = "";
    document.getElementById("id_moduloEdits").selectedIndex = 0;
    document.getElementById("estadoPermisoEdit").checked = true;

    // Limpiar mensajes de error
    document.getElementById("errorNombrePermisoEdit").textContent = "";
    document.getElementById("errorClavePermisoEdit").textContent = "";
    document.getElementById("errorModuloPermisoEdit").textContent = "";

    // También puedes limpiar el ID oculto si quieres
    document.getElementById("id_permisoEdit").value = "";
});



$(document).on('click','.btnEditarPermiso', function(){
const idPermiso = $(this).data('id');
$.ajax({
    url:'../php/obtenerPermisoEdit.php',
    dataType:'json',
    method:'GET',
    data:{idPermiso: idPermiso},
    success: function(response){
        if(response.success){
            console.log(response)
            document.getElementById("id_permisoEdit").value = response.permiso.id_permiso;
      document.getElementById("nombrePermisoEdit").value = response.permiso.descripcion;
      document.getElementById("descripcionPermisoEdit").value = response.permiso.observaciones;
         if(response.permiso.activo == 1){
        document.getElementById("estadoPermisoEdit").checked = true;
      }
      else
      {
        document.getElementById("estadoPermisoEdit").checked = false;
      }
        }
    }
}
)
});