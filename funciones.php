<?php

define("PASSWORD_PREDETERMINADA", "Secundaria_31");
define("HOY", date("Y-m-d"));

function iniciarSesion($usuario, $password){
    $sentencia = "SELECT id, usuario, id_cargo FROM usuarios WHERE usuario = ?";
    $resultado = select($sentencia, [$usuario]);
    if($resultado && count($resultado) > 0){
        $usuario = $resultado[0];
        $verificaPass = verificarPassword($usuario->id, $password);
        if($verificaPass) return $usuario;
    }
    // Return false if the user does not exist or password is incorrect
    return false;
}

function verificarPassword($idUsuario, $password){
    $sentencia = "SELECT password FROM usuarios WHERE id = ?";
    $contrasenia = select($sentencia, [$idUsuario])[0]->password;
    $verifica = password_verify($password, $contrasenia);
    if($verifica) return true;
}

function cambiarPassword($idUsuario, $password){
    $nueva = password_hash($password, PASSWORD_DEFAULT);
    $sentencia = "UPDATE usuarios SET password = ? WHERE id = ?";
    return editar($sentencia, [$nueva, $idUsuario]);
}

function eliminarUsuario($id){
    $sentencia = "DELETE FROM usuarios WHERE id = ?";
    return eliminar($sentencia, $id);
}

function editarUsuario($usuario, $nombre, $telefono, $id_cargo, $id){
    $sentencia = "UPDATE usuarios SET usuario = ?, nombre = ?, telefono = ?, id_cargo = ? WHERE id = ?";
    $parametros = [$usuario, $nombre, $telefono, $id_cargo, $id];
    return editar($sentencia, $parametros);
}

function obtenerUsuarioPorId($id){
    $sentencia = "SELECT id, usuario, nombre, telefono, id_cargo FROM usuarios WHERE id = ?";
    return select($sentencia, [$id])[0];
}

function obtenerUsuarios(){
    $sentencia = "SELECT id, usuario, nombre, telefono, id_cargo FROM usuarios";
    return select($sentencia);
}

function registrarUsuario($usuario, $nombre, $telefono, $id_cargo){
    $password = password_hash(PASSWORD_PREDETERMINADA, PASSWORD_DEFAULT);
    $sentencia = "INSERT INTO usuarios (usuario, nombre, telefono, id_cargo, password) VALUES (?,?,?,?,?)";
    $parametros = [$usuario, $nombre, $telefono, $id_cargo, $password];
    return insertar($sentencia, $parametros);
}

function obtenerNumeroVentas(){
    $sentencia = "SELECT IFNULL(COUNT(*),0) AS total FROM ventas";
    return select($sentencia)[0]->total;
}

function obtenerNumeroUsuarios(){
    $sentencia = "SELECT IFNULL(COUNT(*),0) AS total FROM usuarios";
    return select($sentencia)[0]->total;
}

function obtenerVentasPorUsuario(){
    $sentencia = "SELECT SUM(ventas.total) AS total, usuarios.usuario, COUNT(*) AS numeroVentas 
    FROM ventas
    INNER JOIN usuarios ON usuarios.id = ventas.idUsuario
    GROUP BY ventas.idUsuario
    ORDER BY total DESC";
    return select($sentencia);
}

function obtenerProductosMasVendidos(){
    $sentencia = "SELECT SUM(productos_ventas.cantidad * productos_ventas.precio) AS total, SUM(productos_ventas.cantidad) AS unidades,
    productos.nombre FROM productos_ventas INNER JOIN productos ON productos.id = productos_ventas.idProducto
    GROUP BY productos_ventas.idProducto
    ORDER BY total DESC
    LIMIT 10";
    return select($sentencia);
}

function obtenerTotalVentas($idUsuario = null){
    $parametros = [];
    $sentencia = "SELECT IFNULL(SUM(total),0) AS total FROM ventas";
    if(isset($idUsuario)){
        $sentencia .= " WHERE idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if($fila) return $fila[0]->total;
}

function obtenerTotalVentasHoy($idUsuario = null){
    $parametros = [];
    $sentencia = "SELECT IFNULL(SUM(total),0) AS total FROM ventas WHERE DATE(fecha) = CURDATE() ";
    if(isset($idUsuario)){
        $sentencia .= " AND idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if($fila) return $fila[0]->total;
}

function obtenerTotalVentasSemana($idUsuario = null){
    $parametros = [];
    $sentencia = "SELECT IFNULL(SUM(total),0) AS total FROM ventas  WHERE WEEK(fecha) = WEEK(NOW())";
    if(isset($idUsuario)){
        $sentencia .= " AND  idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if($fila) return $fila[0]->total;
}

function obtenerTotalVentasMes($idUsuario = null){
    $parametros = [];
    $sentencia = "SELECT IFNULL(SUM(total),0) AS total FROM ventas  WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())";
    if(isset($idUsuario)){
        $sentencia .= " AND  idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if($fila) return $fila[0]->total;
}

function calcularTotalVentas($ventas){
    $total = 0;
    foreach ($ventas as $venta) {
        $total += $venta->total;
    }
    return $total;
}

function calcularProductosVendidos($ventas){
    $total = 0;
    foreach ($ventas as $venta) {
        foreach ($venta->productos as $producto) {
            $total += $producto->cantidad;
        }
    }
    return $total;
}

function obtenerGananciaVentas($ventas){
    $total = 0;
    foreach ($ventas as $venta) {
        foreach ($venta->productos as $producto) {
            $total += $producto->cantidad * ($producto->precio - $producto->compra);
        }
    }
    return $total;
}

function obtenerVentas($fechaInicio, $fechaFin, $usuario){
    $parametros = [];
    $sentencia  = "SELECT ventas.*, usuarios.usuario
    FROM ventas 
    INNER JOIN usuarios ON usuarios.id = ventas.idUsuario";

    if(isset($usuario)){
        $sentencia .= " WHERE ventas.idUsuario = ?";
        array_push($parametros, $usuario);
        $ventas = select($sentencia, $parametros);
        return agregarProductosVendidos($ventas);
    }

    if(empty($fechaInicio) && empty($fechaFin)){
        $sentencia .= " WHERE DATE(ventas.fecha) = ? ";
        array_push($parametros, HOY);
        $ventas = select($sentencia, $parametros);
        return agregarProductosVendidos($ventas);
    }

    if(isset($fechaInicio) && isset($fechaFin)){
        $sentencia .= " WHERE DATE(ventas.fecha) >= ? AND DATE(ventas.fecha) <= ?";
        array_push($parametros, $fechaInicio, $fechaFin);
    }

    $ventas = select($sentencia, $parametros);
   
    return agregarProductosVendidos($ventas);
}

function agregarProductosVendidos($ventas){
    foreach($ventas as $venta){
        $venta->productos = obtenerProductosVendidos($venta->id);
    }
    return $ventas;
}

function obtenerProductosVendidos($idVenta){
    $sentencia = "SELECT productos_ventas.cantidad, productos_ventas.precio, productos.nombre,
    productos.compra
    FROM productos_ventas
    INNER JOIN productos ON productos.id = productos_ventas.idProducto
    WHERE idVenta  = ? ";
    return select($sentencia, [$idVenta]);
}

function registrarVenta($productos, $idUsuario, $total){
    $sentencia =  "INSERT INTO ventas (fecha, total, idUsuario) VALUES (?,?,?)";
    $parametros = [date("Y-m-d H:i:s"), $total, $idUsuario];

    $resultadoVenta = insertar($sentencia, $parametros);
    if($resultadoVenta){
        $idVenta = obtenerUltimoIdVenta();
        $productosRegistrados = registrarProductosVenta($productos, $idVenta);
        return $resultadoVenta && $productosRegistrados;
    }
}

function registrarProductosVenta($productos, $idVenta){
    $sentencia = "INSERT INTO productos_ventas (cantidad, precio, idProducto, idVenta) VALUES (?,?,?,?)";
    foreach ($productos as $producto ) {
        $parametros = [$producto->cantidad, $producto->venta, $producto->id, $idVenta];
        insertar($sentencia, $parametros);
        descontarProductos($producto->id, $producto->cantidad);
    }
    return true;
}

function descontarProductos($idProducto, $cantidad){
    $sentencia =  "UPDATE productos SET existencia  = existencia - ? WHERE id = ?";
    $parametros = [$cantidad, $idProducto];
    return editar($sentencia, $parametros);
}

function obtenerUltimoIdVenta(){
    $sentencia  = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    return select($sentencia)[0]->id;
}

function calcularTotalLista($lista){
    $total = 0;
    foreach($lista as $producto){
        $total += floatval($producto->venta * $producto->cantidad);
    }
    return $total;
}

function agregarProductoALista($producto, $listaProductos){
    if($producto->existencia < 1) return $listaProductos;
    $producto->cantidad = 1;
    
    $existe = verificarSiEstaEnLista($producto->id, $listaProductos);

    if(!$existe){
        array_push($listaProductos, $producto);
    } else{
        $existenciaAlcanzada = verificarExistencia($producto->id, $listaProductos, $producto->existencia);
        
        if($existenciaAlcanzada)return $listaProductos;

        $listaProductos = agregarCantidad($producto->id, $listaProductos);
        }
        
    return $listaProductos;
    
}

function verificarExistencia($idProducto, $listaProductos, $existencia){
    foreach($listaProductos as $producto){
        if($producto->id == $idProducto){
           if($existencia <= $producto->cantidad) return true; 
        }
    }
    return false;
}

function verificarSiEstaEnLista($idProducto, $listaProductos){
    foreach($listaProductos as $producto){
        if($producto->id == $idProducto){
            return true;
        }
    }
    return false;
}

function agregarCantidad($idProducto, $listaProductos){
    foreach($listaProductos as $producto){
        if($producto->id == $idProducto){
            $producto->cantidad++;
        }
    }
    return $listaProductos;
}

function obtenerProductoPorCodigo($codigo){
    $sentencia = "SELECT * FROM productos WHERE codigo = ?";
    $producto = select($sentencia, [$codigo]);
    if($producto) return $producto[0];
    return [];
}

function obtenerNumeroProductos(){
    $sentencia = "SELECT IFNULL(SUM(existencia),0) AS total FROM productos";
    $fila = select($sentencia);
    if($fila) return $fila[0]->total;
}

function obtenerTotalInventario(){
    $sentencia = "SELECT IFNULL(SUM(existencia * venta),0) AS total FROM productos";
    $fila = select($sentencia);
    if($fila) return $fila[0]->total;
}

function calcularGananciaProductos(){
    $sentencia = "SELECT IFNULL(SUM(existencia*venta) - SUM(existencia*compra),0) AS total FROM productos";
    $fila = select($sentencia);
    if($fila) return $fila[0]->total;
}

function eliminarProducto($id){
    $sentencia = "DELETE FROM productos WHERE id = ?";
    return eliminar($sentencia, $id);
}

function editarProducto($codigo, $nombre, $compra, $venta, $existencia, $id){
    $sentencia = "UPDATE productos SET codigo = ?, nombre = ?, compra = ?, venta = ?, existencia = ? WHERE id = ?";
    $parametros = [$codigo, $nombre, $compra, $venta, $existencia, $id];
    return editar($sentencia, $parametros);
}

function obtenerProductoPorId($id){
    $sentencia = "SELECT * FROM productos WHERE id = ?";
    return select($sentencia, [$id])[0];
}

function obtenerProductos($busqueda = null){
    $parametros = [];
    $sentencia = "SELECT * FROM productos ";
    if(isset($busqueda)){
        $sentencia .= " WHERE nombre LIKE ? OR codigo LIKE ?";
        array_push($parametros, "%".$busqueda."%", "%".$busqueda."%"); 
    } 
    return select($sentencia, $parametros);
}

function registrarProducto($codigo, $nombre, $compra, $venta, $existencia){
    $sentencia = "INSERT INTO productos(codigo, nombre, compra, venta, existencia) VALUES (?,?,?,?,?)";
    $parametros = [$codigo, $nombre, $compra, $venta, $existencia];
    return insertar($sentencia, $parametros);
}

function select($sentencia, $parametros = []){
    $bd = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    $respuesta->execute($parametros);
    return $respuesta->fetchAll();
}

function insertar($sentencia, $parametros ){
    $bd = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute($parametros);
}

function eliminar($sentencia, $id ){
    $bd = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute([$id]);
}

function editar($sentencia, $parametros ){
    $bd = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute($parametros);
}

function conectarBaseDatos() {
	$host = "localhost";
	$db   = "ventas_php";
	$user = "root";
	$pass = "";
	$charset = 'utf8mb4';

	$options = [
	    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
	    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
	    \PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	try {
	     $pdo = new \PDO($dsn, $user, $pass, $options);
	     return $pdo;
	} catch (\PDOException $e) {
	     throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
}
