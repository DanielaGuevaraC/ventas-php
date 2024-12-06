<?php
include_once "encabezado.php";
include_once "navbar.php";
include_once "funciones.php";
session_start();
if(empty($_SESSION['idUsuario'])) header("location: login.php");
if($_SESSION['id_cargo'] != 1) {
    ?>
     <div class="container">
    <div class="alert alert-info" role="alert">
        <h1>
            Hola, <?= $_SESSION['usuario']?>
        </h1>
    </div>
    <div class="container">
        <div class="alert alert-danger" role="alert">
            No tienes permitido acceder al apartado de usuarios
        </div>
    </div>
    <?php 
    exit; 
}


$usuarios = obtenerUsuarios();
?>
<div class="container">
    <h1>
        <a class="btn btn-success btn-lg" href="agregar_usuario.php">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
        Usuarios
    </h1>
    <table class="table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Cargo</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($usuarios as $usuario){
            ?>
                <tr>
                    <td><?php echo $usuario->usuario; ?></td>
                    <td><?php echo $usuario->nombre; ?></td>
                    <td><?php echo $usuario->telefono; ?></td>
                    <td><?php echo $usuario->id_cargo; ?></td>

                    <td>
                        <a class="btn btn-info" href="editar_usuario.php?id=<?php echo $usuario->id; ?>">
                            <i class="fa fa-edit"></i>
                            Editar
                        </a>
                    </td>
                    <td>
                        <a class="btn btn-danger" href="eliminar_usuario.php?id=<?php echo $usuario->id; ?>">
                            <i class="fa fa-trash"></i>
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>