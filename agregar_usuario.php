<?php
include_once "encabezado.php";
include_once "navbar.php";
session_start();

if(empty($_SESSION['usuario'])) header("location: login.php");

?>
<div class="container">
    <h3>Agregar usuario</h3>
    <form method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Nombre de usuario</label>
            <input type="text" name="usuario" class="form-control" id="usuario" placeholder="Escribe el nombre de usuario. Ej. Paco">
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Escribe el nombre completo del usuario">
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" id="telefono" placeholder="Ej. 2111568974">
        </div>
        <div class="mb-3">
            <label for="id_cargo" class="form-label">Cargo</label>
            <input type="text" name="id_cargo" class="form-control" id="cargo" placeholder="Ej. 2 (2= Usuario)">
        </div>
        <div class="text-center mt-3">
            <input type="submit" name="registrar" value="Registrar" class="btn btn-primary btn-lg">
            
            </input>
            <a href="usuarios.php" class="btn btn-danger btn-lg">
                <i class="fa fa-times"></i> 
                Cancelar
            </a>
        </div>
    </form>
</div> 
    <?php
if(isset($_POST['registrar'])){
    $usuario = $_POST['usuario'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $id_cargo = $_POST['id_cargo'];
    
    if(empty($usuario) || empty($nombre) || empty($telefono) || empty($id_cargo)){
        echo '
        <div class="alert alert-danger mt-3" role="alert">
            Debes completar todos los datos.
        </div>';
        return;
    } 
    
    include_once "funciones.php";
    $resultado = registrarUsuario($usuario, $nombre, $telefono, $id_cargo);
    
    if($resultado){
        echo '
        <div class="alert alert-success mt-3" role="alert">
            Usuario registrado con éxito.
        </div>';
    }
    
}
?>