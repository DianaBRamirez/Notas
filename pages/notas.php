<?php include("../header.php");
session_start();
?>

<main id="main" class="main">

    <section class="section dashboard ">
        <div class="container mt-4">
            <!-- INICIO Mensajes Exp5 -->
            <?php
            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">'
                    . htmlspecialchars($_SESSION['message']) .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                unset($_SESSION['message']);
            }
            ?>
            <!-- FIN Mensajes Exp5 -->

            <!-- INICIO Texto Exp6  -->
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Resumen de notas </h2>
            </div>
            <div class="alert alert-primary nopadding mt-2 mb-3" role="alert">
                <i class="bi bi-gear-fill"> Herramientas de programación</i>
            </div>
            <!-- FIN Texto Exp6  -->

            <!-- INICIO TablaNotas Exp7 -->
            <table id="tablaNotas" class="table table-bordered " cellspacing="0" width="100%">
                <thead>
                    <tr> <!--  encabezados de la tabla -->
                        <th>Rut</th>
                        <th>Nombre</th>
                        <th>Nota 1</th>
                        <th>Nota 2</th>
                        <th>Nota 3</th>
                        <th>Promedio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    // Abrir archivos
                    $archivo_estudiantes = fopen("../doc/estudiantes.txt", "r");
                    $archivo_notas = file("../doc/notas.txt");

                    if ($archivo_estudiantes) {
                        while (($linea_estudiante = fgets($archivo_estudiantes)) !== false) {
                            $datos_estudiante = explode("|", $linea_estudiante); // Separar los datos por el delimitador '|'
                            $id = trim($datos_estudiante[0]); //id
                            $rut = trim($datos_estudiante[1]);  // Eliminar espacios en blanco y asignar los valores de RUT 
                            $nombre = trim($datos_estudiante[2]);  // Eliminar espacios en blanco
                            $nota1 = $nota2 = $nota3 = "N/A"; // Default a "N/A" si no se encuentran notas

                            // Buscar las notas correspondientes al id en notas.txt
                            foreach ($archivo_notas as $linea_nota) {
                                $datos_nota = explode("|", trim($linea_nota));
                                if ($datos_nota[1] == $id) {
                                    $idEstudiante =  $datos_nota[1];
                                    $nota1 = $datos_nota[2];
                                    $nota2 = $datos_nota[3];
                                    $nota3 = $datos_nota[4];
                                    break; // Terminar la búsqueda una vez que se encuentran las notas
                                }
                            }

                            // Calcular el promedio de las notas si todas están disponibles
                            if ($nota1 !== "Pendiente" && $nota2 !== "Pendiente" && $nota3 !== "Pendiente") {
                                $promedio = ($nota1 + $nota2 + $nota3) / 3;
                                $promedio = number_format($promedio, 2, '.', '');
                                $estado = ($promedio < 4) ? "Reprobado" : "Aprobado"; // Condición para verificar si está reprobado
                            } else {
                                $promedio = "Pendiente";
                                $estado = "Pendiente";
                            }

                            // Generar las filas de la tabla con los datos recuperados
                            echo "<tr>
                                    <td>$rut</td>
                                    <td>$nombre</td>
                                    <td>$nota1</td>
                                    <td>$nota2</td>
                                    <td>$nota3</td>
                                    <td>$promedio</td>
                                    <td>$estado</td>
                                    <td>
                                        <span title='Editar notas'> 
                                            <button type='button' name='editar' id='editar' class='btn btn-outline-dark' onclick=\"openEditModal('$idEstudiante','$rut', '$nombre','$nota1','$nota2','$nota3')\" data-bs-toggle='modal' data-bs-target='#editNotesModal'>
                                                <i class='bi bi-pencil-square'></i>
                                            </button>
                                        </span>
                                        <span title='Eliminar'> 
                                            <a type='button' id='eliminarNotas' name='eliminarNotas' onclick='return eliminarNotas()' href='eliminarNotas.php?id=" . htmlspecialchars($id) . "' class='btn btn-outline-danger'><i class='bi bi-trash'></i>
                                            </a>
                                        </span>
                                    </td>
                                  </tr>";
                        }
                        fclose($archivo_estudiantes); 
                    } else {
                        echo "No se pudo abrir el archivo";
                    }
                    ?>
                </tbody>
            </table>
            <!-- INICIO TablaNotas Exp7 -->
    </section>
</main>


<!-- INICIO EditarNotas exp8 -->
<div class="modal fade" id="editNotesModal" tabindex="-1" aria-labelledby="editNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #007bff; color: white;">
                <h5 class="modal-title" id="editNotesModalLabel"><i class="bi bi-pencil-square"></i> Editar Notas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="editarNotas.php">
                    <input type="hidden" value="" name="idEstudiante" id="idEstudiante">
                    <div class="mb-3">
                        <label for="studentRut" class="form-label">RUT del estudiante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control" id="rut" name="rut" value="" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="studentName" class="form-label">Nombre del estudiante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="studentName" name="studentName" value="" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nota1" class="form-label">Nota 1</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-award"></i></span>
                            <input type="number" class="form-control" id="nota1" name="nota1" min="1" max="7" step="0.1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nota2" class="form-label">Nota 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-award"></i></span>
                            <input type="number" class="form-control" id="nota2" name="nota2" min="1" max="7" step="0.1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nota3" class="form-label">Nota 3</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-award"></i></span>
                            <input type="number" class="form-control" id="nota3" name="nota3" min="1" max="7" step="0.1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="GuardarNotas" name="GuardarNotas">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- FIN EditarNotas exp8 -->

<?php include("../footer.php"); ?>