
function loadMenu() {
    return fetch('../../menu/html/menu.html')
        .then(response => {
            if (!response.ok) {
                throw new Error('No se pudo cargar el menú');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('menu-container').innerHTML = html;
            initializeMenu(); // Inicializar funcionalidades del menú
        })
        .catch(error => {
            console.error('Error al cargar el menú:', error);
            document.getElementById('menu-container').innerHTML = '<p>Error al cargar el menú</p>';
        });
}


function initializeMenu() {
    // Controlar submenús
    document.querySelectorAll('.submenu-header').forEach(header => {
        header.addEventListener('click', function() {
            const parentItem = this.closest('.with-submenu');
            parentItem.classList.toggle('expanded');
            
            // Rotar icono
            const icon = this.querySelector('.submenu-icon');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });
    
    // Botón para mostrar/ocultar menú
    const toggleBtn = document.getElementById('toggleMenuBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.getElementById('navVertical').classList.toggle('collapsed');
        });
    }
}

document.addEventListener('DOMContentLoaded', async function(){
    await loadMenu();   

    $.ajax({
        url:'../php/obtenerDatosModulo.php',
        method:'GET',
        dataType:'JSON',
        success:function(response){
            if(response.success){
                console.log("modulos cargados"+ response.modulos)
                llenarTablaModulos(response.modulos)
                console.log("permisos de acceso", response.permisosDetalle)
                console.log("modulos de acceso",response.moduloss)
                configurarPermisosModulos(response.moduloss)
                document.getElementById("total-modulos").textContent = response.totalModulos.total_modulos
                document.getElementById("totalModuloActivo").textContent = response.totalModulos.modulo_activos
                document.getElementById("totalModuloInactivo").textContent = response.totalModulos.modulo_inactivos
            }
            else{
                console.log("error");
            }
        }
    })


})

function configurarPermisosModulos(modulos){
       //validación para el moludo de los croquis
             if(modulos.includes("Croquis")){
              document.getElementById("menu-croquis").style.display = 'block'
             }
            if(modulos.includes("Diagnostico")){
              document.getElementById("menu-diagnostico").style.display = 'block'
            }
            if(modulos.includes("Comunidades")){
              document.getElementById("menu-comunidad").style.display = 'flex'
            }
            if(modulos.includes("Departamentos")){
              document.getElementById("menu-departamento").style.display = "flex"
            }
            if(modulos.includes("Familias")){
              document.getElementById("menu-familias").style.display = 'flex'
            }
            if(modulos.includes("Usuarios")){
              document.getElementById("menu-usuarios").style.display = "flex"
            }
            if(modulos.includes("Reportes")){
              document.getElementById("menu-reportes").style.display = "flex"
            } 
            
}


function llenarTablaModulos(modulos)
{
    const $tabla = $("#tablaModulos");
    const $tbody = $tabla.find('tbody');

    $tbody.empty();

    if(modulos.length ===0){
         const fila = `
          <tr>
            <td colspan="6" style="text-align: center;">No hay ningún mudulo registrado</td>
          </tr>
        `;
        $tbody.append(fila);
    }
else{
    modulos.forEach(modulo=>{
        const activo = modulo.activo ==1?
        '<span class="badge bg-success">Activo</span>':
        '<span class="badge bg-danger">Inactivo</span>';
        const fila = `
        <tr>
            <td >
            ${modulo.id_modulo}
    </td>                               
        <td>
                                        
                                            <div class="fw-bold">${modulo.nombre}</div>
                                            <small class="text-muted">${modulo.descripcion}</small>
                                        </td>
                                        <td><i class="${modulo.icono}"></i></td>
                                        <td>${activo}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btnEditModulo" title="Editar" data-bs-toggle="modal" data-id="${modulo.id_modulo}" data-bs-target="#modalModuloEdit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                           
                                        </td>
                                    </tr>
        `;
        $tbody.append(fila);
    });
    $tabla.DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
}
}


$(document).on('click', '.btnSaveModulo', function () {
    const nombreModulo = document.getElementById("moduloNombre").value.trim();
    const descripcion = document.getElementById("moduloDescripcion").value.trim();
    const icono = document.getElementById("moduloIcono").value.trim();
    const errorNombre = document.getElementById("errorNombreModulo");

    // Limpiar error anterior
    errorNombre.textContent = "";

    // Validación
    if (nombreModulo === "") {
        errorNombre.textContent = "El nombre del módulo es obligatorio.";
        return;
    }

    // Si pasa la validación, continúa con la petición
    $.ajax({
        url: '../php/registrarModulo.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            nombreModulo: nombreModulo,
            descripcion: descripcion,
            icono: icono
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Módulo registrado correctamente.',
                    confirmButtonColor: '#3085d6',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo registrar el módulo.',
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

$(document).on('click', '.btnEditModulo', function(){
    const idModulo = $(this).data('id');
    $.ajax({
        url:'../php/obtenerModuloEdit.php',
        method:'GET',
        dataType:'JSON',
        data:{
            idModulo : idModulo
        },
        success:function(response){
            if(response.success){
                document.getElementById("idModuloIdEdit").value= response.datoModulo.id_modulo
                document.getElementById("moduloNombreEdit").value = response.datoModulo.nombre
                document.getElementById("moduloDescripcionEdit").value = response.datoModulo.descripcion
                document.getElementById("estadoModulo").checked = response.datoModulo.activo

            }
        }
    });
});

$(document).on('click', '.btnEditModuloSave', function(){
    const idModulo = document.getElementById("idModuloIdEdit").value;
    const nombreModuloEdit = document.getElementById("moduloNombreEdit").value;
    const descripcionEdit = document.getElementById("moduloDescripcionEdit").value;
    const moduloIconoEdit = document.getElementById("moduloIconoEdit").value;
    const estado = document.getElementById("estadoModulo").checked ? 1:0;
    $.ajax({
        url:'../php/actualizarModulo.php',
        dataType:'JSON',
        method:'POST',
        data:{
            idModulo : idModulo,
            nombreModuloEdit : nombreModuloEdit,
            descripcionEdit : descripcionEdit,
            moduloIconoEdit : moduloIconoEdit,
            estado : estado
        },
        success: function(response){
            if(response.success){
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Módulo actualizado correctamente.',
                    confirmButtonColor: '#3085d6',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            }
        }
    });
});
   
