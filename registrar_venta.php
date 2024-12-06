<?php
include_once "funciones.php";


session_start();
$productos = $_SESSION['lista'];
$idUsuario = $_SESSION['idUsuario'];
$total = calcularTotalLista($productos);

if(count($productos) === 0) {
    header("location: vender.php");
    return;
};
$resultado = registrarVenta($productos, $idUsuario, $total);

if(!$resultado) {
    echo "Error al registrar la venta";
    return;
}

$_SESSION['lista'] = [];

echo "
<script type='text/javascript'>
    window.location.href='vender.php'
    alert('Venta realizada con éxito')
</script>";
//header("location: vender.php");

?>