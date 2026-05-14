

document.addEventListener("DOMContentLoaded", async function() {

getSession();

});

function getSession(){
  $.ajax({
    url:'../php/consultaInformacionUsuarios.php',
    method: 'GET',
    dataType: 'json',
    success: function(response){
        if(response.success){
            console.log(response);
            llenarTablaUsuarios(response.listUsuarios);

            document.getElementById("totalUsuarios").textContent = response.totalUsuarios;
            document.getElementById("totalUsText").textContent = response.totalUsuarios ;  
            document.getElementById("totalActivosText").textContent = response.conteoUsuariosActivos.activos ;
            document.getElementById("totalInactivosText").textContent = response.conteoUsuariosActivos.inactivos ;
            document.getElementById("totalAdmonText").textContent = response.cantAdmonRolT
            console.log("roles" + response.cantAdmonRolT)

            console.log("permisos", response.permisosDetalle);
            console.log("modulos", response.modulos)
          
          }

        }
      


});



}

let isPasswordValid = false;
$("#password").on("input", function() {
  validarPasswordLength($(this).val());
  checkPasswordsMatch();
  updateSubmitButton();

});
$("#confirmPassword").on("input", function() {
  checkPasswordsMatch();
  updateSubmitButton();
});

 // Toggle visibilidad de contraseñas
      $('#toggleNewPassword').click(function() {
        togglePasswordVisibility('password', $(this));
      });

      $('#toggleConfirmPassword').click(function() {
        togglePasswordVisibility('confirmPassword', $(this));
      });

      function togglePasswordVisibility(inputId, button) {
        const input = $('#' + inputId);
        const icon = button.find('i');
        
        if (input.attr('type') === 'password') {
          input.attr('type', 'text');
          icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          input.attr('type', 'password');
          icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
      }
function validarPasswordLength(password) {
const requirements = {
          length: password.length >= 8,
          upper: /[A-Z]/.test(password),
          lower: /[a-z]/.test(password),
          number: /[0-9]/.test(password),
          special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        updateRequirementIcon('reqLength', requirements.length);
        updateRequirementIcon('reqUpper', requirements.upper);
        updateRequirementIcon('reqLower', requirements.lower);
        updateRequirementIcon('reqNumber', requirements.number);
        updateRequirementIcon('reqSpecial', requirements.special);

isPasswordValid = Object.values(requirements).every(Boolean);
}
function updateRequirementIcon(elementId, isValid){
   const icon = $('#' + elementId);
        if (isValid) {
          icon.removeClass('requirement-invalid').addClass('requirement-valid');
          icon.removeClass('fa-times').addClass('fa-check');
        } else {
          icon.removeClass('requirement-valid').addClass('requirement-invalid');
          icon.removeClass('fa-check').addClass('fa-times');
        }
}
      function checkPasswordsMatch() {
        const newPassword = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        const matchElement = $('#passwordMatch');
        
        if (!newPassword) {
          matchElement.html('');
          doPasswordsMatch = false;
          return;
        }
        
        if (!confirmPassword) {
          matchElement.html('<span class="match-invalid"><i class="fas fa-times"></i> Confirma tu contraseña</span>');
          doPasswordsMatch = false;
          return;
        }
        
        if (newPassword === confirmPassword) {
          matchElement.html('<span class="match-valid"><i class="fas fa-check"></i> Las contraseñas coinciden</span>');
          doPasswordsMatch = true;
        } else {
          matchElement.html('<span class="match-invalid"><i class="fas fa-times"></i> Las contraseñas no coinciden</span>');
          doPasswordsMatch = false;
        }
      }
      function updateSubmitButton() {
        const button = $('#btnAddUsuario');
        if (isPasswordValid && doPasswordsMatch) {
          button.prop('disabled', false);
        } else {
          button.prop('disabled', true);
        }
      }

$(document).on('click','.btnAgregarUsuario', function(){

  const nombre = document.getElementById("nombres").value;
  const apellido = document.getElementById("apellidos").value;
  const direccion = document.getElementById("direccion").value;
  const telefono = document.getElementById("telefono").value;
  const correo = document.getElementById("email").value;
  const contrasena = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  const emailRecuperacion= document.getElementById("emailRecuperacion").value;
  //variables para manejar los errores
  const errorUsuarioNombre = document.getElementById("errorUsuarioNombre");
  const errorUsuarioApellido = document.getElementById("errorUsuarioApellido");
  const errorUsuarioDireccion = document.getElementById("errorUsuarioDireccion");
  const errorUsuarioTelefono = document.getElementById("errorUsuarioTelefono");
  const errorUsuarioCorreo = document.getElementById("errorUsuarioCorreo");
  const errorUsuarioContrasena = document.getElementById("errorUsuarioContrasena");
  const errorUsuarioConfirmContrasena = document.getElementById("errorUsuarioConfirmContrasena");

 if(nombre.trim()===""){
    errorUsuarioNombre.textContent = "El nombre no puede estar vacío";
    return false;
  }
  if(apellido.trim()===""){
    errorUsuarioApellido.textContent = "El apellido no puede estar vacío";
    return false;
  }
  if(direccion.trim()===""){
    errorUsuarioDireccion.textContent = "La dirección no puede estar vacía";
    return false;
  }
  if(telefono.trim()===""){
    errorUsuarioTelefono.textContent = "El teléfono no puede estar vacío";
    return false;
  }
  if(correo.trim()===""){
    errorUsuarioCorreo.textContent = "El correo no puede estar vacío";
    return false;
  }
  if(contrasena.trim()===""){
    errorUsuarioContrasena.textContent = "La contraseña no puede estar vacía";
    return false;
  }
  if(confirmPassword.trim()===""){
    errorUsuarioConfirmContrasena.textContent = "Debe confirmar la contraseña";
    return false;
  }
  if(contrasena !== confirmPassword){
    errorUsuarioContrasena.textContent = "Las contraseñas no coinciden";
    errorUsuarioConfirmContrasena.textContent = "Las contraseñas no coinciden";
    return false;
  }

   
  $.ajax({
    url:'../php/registrarUsuario.php',
    method: 'POST',
    data: {
      nombre_usuario: nombre,
      apellido_usuario: apellido,
      direccion: direccion,
      telefono: telefono,
      correo: correo,
      contrasena: contrasena,
      email_recuperacion: emailRecuperacion
    },
    dataType: 'json',
    success: function(response){
      if(response.success){
          Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: 'Usuario registrado correctamente.',
    background: '#3a0ca3', // Fondo verde
    color: '#fff', // Texto blanco
    confirmButtonColor: '#4caf50', // Color del botón de confirmación
    timer: 1500,
    timerProgressBar: true,
    customClass: {
      
    popup: 'custom-swal-popup'
  },
}).then(() => {
    // Recargar página o cualquier acción posterior
    location.reload();
});
      }
      else{
        alert("Error al registrar el usuario: " + response.message);
      }
    }
  })

});

//funciones para limpiar los errores al escribir en los campos
document.addEventListener("input", function(event) {
  event.preventDefault();
  document.getElementById("errorUsuarioNombre").textContent = "";
  document.getElementById("errorUsuarioApellido").textContent = "";
  document.getElementById("errorUsuarioDireccion").textContent = "";
  document.getElementById("errorUsuarioTelefono").textContent = "";
  document.getElementById("errorUsuarioCorreo").textContent = "";
  document.getElementById("errorUsuarioContrasena").textContent = "";
  document.getElementById("errorUsuarioConfirmContrasena").textContent = "";


});

function llenarTablaUsuarios(usuarios) {
 
    const $tabla = $('#usersTable');
    const $tbody = $tabla.find('tbody');

    $tbody.empty();
 
    if (usuarios.length === 0) {

        const fila = `
          <tr>
            <td colspan="6" style="text-align: center;">No hay ningún habitante registrado</td>
          </tr>
        `;
        $tbody.append(fila);
    } else {
        usuarios.forEach(item => {
          const activo = item.activo == 1 ? 'Activo' : 'Inactivo';
          const colorEstado = item.activo==1 ?'badge-active' :'badge-inactive';
            const fila = `
               <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="user-avatar me-3 inicialText">${item.inicial}</span>

                                                <div>
                                                    <div class="fw-bold">${item.nombre_usuario}</div>
                                                    <small class="text-muted">${item.correo}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-admin">${item.nombre || 'Sin rol'}</span></td>
                                        <td><span class="badge badge-status ${colorEstado}">${activo}</span></td>
                                        
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btnEditarUsuario" data-bs-toggle="modal" data-bs-target="#EditUsuarioModal" data-id="${item.id_usuario}" title="Editar">
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

$(document).on('click','.btnEditarUsuario', function(){
const idUsuario = $(this).data('id');

//cargar datos al usuario en el modal para editarlo
$.ajax({
  url:'../php/obtenerUsuario.php',
  method: 'GET',
  data: {id_usuario: idUsuario},
  dataType: 'json',
  success: function(response){
    if(response.success){
      console.log(response.usuario);
      document.getElementById("idUsuarioEdit").value = response.usuario.id_usuario;
      document.getElementById("nombreUsuarioEdit").value = response.usuario.nombre_usuario;
      document.getElementById("apellidoUsuarioEdit").value = response.usuario.apellido_usuario;
      document.getElementById("direccionUsuarioEdit").value = response.usuario.direccion;
      document.getElementById("telefonoUsuarioEdit").value = response.usuario.telefono;
      document.getElementById("correoElectronicoUsuarioEdit").value = response.usuario.correo;
      if(response.usuario.activo == 1){
        document.getElementById("estadoUsuarioEdit").checked = true;
      }
      else
      {
        document.getElementById("estadoUsuarioEdit").checked = false;
      }

    }
  }
})
});

//button para enviar y actualizar al usuario
function updateUsuario(){
  const idUsuario = document.getElementById("idUsuarioEdit").value;
  const nombre = document.getElementById("nombreUsuarioEdit").value;
  const apellido = document.getElementById("apellidoUsuarioEdit").value;
  const direccion = document.getElementById("direccionUsuarioEdit").value;
  const telefono = document.getElementById("telefonoUsuarioEdit").value;
  const correo = document.getElementById("correoElectronicoUsuarioEdit").value;
  const estado = document.getElementById("estadoUsuarioEdit").checked ? 1 : 0;
  $.ajax({
    url:'../php/actualizarUsuario.php',
    method: 'POST',
    data: {
      id_usuario: idUsuario,
      nombre_usuario: nombre,
      apellido_usuario: apellido,
      direccion: direccion,
      telefono: telefono,
      correo: correo,
      activoUs: estado
    },
    dataType: 'json',
    success: function(response){
      if(response.success){
       Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: 'Usuario actualizado correctamente.',
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
      else{
        alert("Error al actualizar el usuario");
      }  

}
  })
}


function mostrarAlerta(mensaje, tipo) {
    const alerta = `
        <div class="alert alert-${tipo} alert-auto-close alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('#alertContainer').append(alerta);
    
    // Remover la alerta después de 5 segundos
    setTimeout(() => {
        $('.alert-auto-close').alert('close');
    }, 5000);
}