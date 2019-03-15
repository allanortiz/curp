<?php
require_once('./Curp.php');

$nombre          = ''; 
$apellidoPaterno = '';
$apellidoMaterno = '';
$diaNacimiento   = ''; // XX
$mesNacimiento   = ''; // XX
$anioNacimiento  = ''; // XXXX
$fecha           = "$anioNacimiento-$mesNacimiento-$diaNacimiento";
$sexo            = ''; // X (H o M)
$entidad         = ''; // XX (01-32, 87-88)

$otra_curp   = '';
$diferencias = [];

try
{
    $curpObj     = new Curp($nombre, $apellidoPaterno, $apellidoMaterno, $fecha, $sexo, $entidad);
    $curp        = $curpObj->curp;
    $diferencias = $curpObj->comparar($otra_curp);
}
catch(Exception $e)
{
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CURP</title>
</head>
<body>
    <h1>CURP</h1>
    <h2>Nombre: <?php echo $nombre . " " . $apellidoPaterno . " " . $apellidoMaterno; ?></h1>
    <h2>CURP: <?php echo $curp; ?></h2>
    <br>
    <h2>Comparación de CURP</h2>
    <p>Diferencias encontradas al validar con la curp <strong><?php echo $otra_curp; ?></strong>:</p>
    <p><strong><?php echo $diferencias["curp_formateada"] ?></strong></p>
    <dl>
        <?php
        foreach ($diferencias["detalles"] as $codigo => $d) 
        {
        ?>

        <dt><?php echo $codigo; ?></dt>
        <dd>- Índices: <?php echo implode(', ', $d["indices"]); ?></dd>
        <dd>- Mensaje: <?php echo $d["mensaje"]; ?></dd>

        <?php  
        }
        ?>
    </dl>
</body>
</html>
