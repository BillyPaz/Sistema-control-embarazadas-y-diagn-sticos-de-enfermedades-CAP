<?php include(__DIR__."/../conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ingresar Pacientes a Tuberculosis</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
   

    h2 {
      text-align: center;
      color: #02457a;
      margin-bottom: 25px;
    }

    #buscador {
      display: block;
      margin: 0 auto;
      padding: 10px 15px;
      width: 60%;
      max-width: 500px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    #buscador:focus {
      border-color: #02457a;
      outline: none;
      box-shadow: 0 0 6px rgba(2, 69, 122, 0.2);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      border-radius: 12px;
      overflow: hidden;
    }

    th {
      background-color: #02457a;
      color: #fff;
      padding: 12px;
      text-align: left;
      font-weight: 600;
      font-size: 14px;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }

    tr:hover {
      background-color: #f3f8fc;
      transition: background 0.2s;
    }

    .btn {
      background-color: #02457a;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 14px;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #018ABE;
    }
    
    .main {
        padding: 20px;
    }

    #tablaResultados tbody:empty::after {
      content: "Busca al paciente para poder agregarlo";
      display: block;
      text-align: center;
      padding: 20px;
      color: #666;
    }
  </style>
  <link rel="stylesheet" href="../css/pacientes.css">
</head>
<body>

<?php include("../MENU/menuVIH.php") ?>

<main>

 <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>REGISTRAR PACIENTES</h1>
        </div>
    </div>
    </header>


  <input type="text" id="buscador" placeholder="Buscar por nombre o DPI">

  <table id="tablaResultados">
    <thead>
      <tr>
        <th>DPI</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
 
  <script>
  document.getElementById('buscador').addEventListener('input', function() {
    const valor = this.value.trim();

    fetch('buscar_pacientes.php?term=' + encodeURIComponent(valor))
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector('#tablaResultados tbody');
        tbody.innerHTML = '';

        if (data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No se encontraron pacientes</td></tr>';
          return;
        }

        data.forEach(paciente => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${paciente.dpi_pacientes}</td>
            <td>${paciente.nombres_pacientes}</td>
            <td>${paciente.apellidos_pacientes}</td>
            <td>
              <button class='btn' onclick="confirmarIngreso(${paciente.id_pacientes})">
                Ingresar
              </button>
            </td>
          `;
          tbody.appendChild(fila);
        });
      });
  });

  function confirmarIngreso(idPaciente) {
    Swal.fire({
      title: 'Atención',
      text: 'Está a punto de ingresar al paciente en Tuberculosis. ¿Está seguro de realizar esta operación?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, ingresar',
      cancelButtonText: 'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'tuberculosis.php?id_paciente=' + idPaciente;
      } else {
        window.location.href = 'pacientesT.php';
      }
    });
  }
  </script>
 </main>
 <script src="script.js"></script>
</body>
</html>


