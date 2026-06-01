<?php
session_start();

// Base de datos simulada
$usuarios = [
    ["correo" => "admin@granjitas.com", "clave" => "1234", "rol" => "admin"],
    ["correo" => "cliente@granjitas.com", "clave" => "1234", "rol" => "cliente"]
];

$granjas = [
    [
        "nombre" => "Granja La Esperanza",
        "ubicacion" => "Sonsonate",
        "descripcion" => "Productos frescos del campo.",
        "productos" => [
            ["nombre" => "Huevos", "precio" => 2.50],
            ["nombre" => "Leche", "precio" => 1.00]
        ]
    ],
    [
        "nombre" => "Finca Verde",
        "ubicacion" => "Santa Ana",
        "descripcion" => "Cultivos orgánicos.",
        "productos" => [
            ["nombre" => "Tomate", "precio" => 1.50],
            ["nombre" => "Lechuga", "precio" => 0.75]
        ]
    ]
];

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// LÓGICA: AGREGAR AL CARRITO
if (isset($_POST['agregar_carrito'])) {
    $_SESSION['carrito'][] = [
        "nombre" => $_POST['nombre'],
        "precio" => $_POST['precio']
    ];
}

// LÓGICA: PROCESAR PEDIDO
if (isset($_POST['pedir'])) {
    $_SESSION['mensaje'] = "✅ Su pedido está en camino.";
    $_SESSION['carrito'] = [];
}

// LÓGICA: ENVIAR RESEÑA (Corregido y añadido backend)
if (isset($_POST['enviar_resena'])) {
    $granja_nombre = $_POST['granja_nombre'];
    $comentario = htmlspecialchars($_POST['comentario']);
    $_SESSION['mensaje'] = "💬 Reseña enviada para <b>$granja_nombre</b>: \"$comentario\"";
}

// LÓGICA: INICIAR SESIÓN
if (isset($_POST['login'])) {
    foreach ($usuarios as $u) {
        if ($_POST['correo'] == $u['correo'] && $_POST['clave'] == $u['clave']) {
            $_SESSION['usuario'] = $u;
            break;
        }
    }
    if (!isset($_SESSION['usuario'])) {
        $_SESSION['mensaje'] = "❌ Correo o contraseña incorrectos.";
    }
}

// LÓGICA: CERRAR SESIÓN
if (isset($_POST['logout'])) {
    unset($_SESSION['usuario']);
    $_SESSION['carrito'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granjitas</title>
    <style>
        *, *:before, *:after {
            box-sizing: border-box; /* Evita que los inputs se salgan de las tarjetas */
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        header {
            background: #2d6a4f;
            color: white;
            padding: 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
        }

        button {
            background: #2d6a4f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1b4332;
        }

        .producto {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-danger {
            background: #b7094c;
        }
        .btn-danger:hover {
            background: #890637;
        }

        h1, h2, h3, h4 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<header>
    <div class="container header-content">
        <h1>🌾 Granjitas</h1>
        <?php if (isset($_SESSION['usuario'])) { ?>
            <div>
                <p style="margin: 0 0 10px 0;">Bienvenido: <b><?php echo $_SESSION['usuario']['correo']; ?></b> (<?php echo ucfirst($_SESSION['usuario']['rol']); ?>)</p>
                <form method="post">
                    <button type="submit" name="logout" class="btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        <?php } ?>
    </div>
</header>

<div class="container">

    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<div class='card' style='border-left: 5px solid #2d6a4f;'>";
        echo "<p style='margin:0;'>" . $_SESSION['mensaje'] . "</p>";
        echo "</div>";
        unset($_SESSION['mensaje']);
    }
    ?>

    <?php if (!isset($_SESSION['usuario'])) { ?>
        <div class="card">
            <h2>Iniciar Sesión</h2>
            <form method="post">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" placeholder="ejemplo@granjitas.com" required>

                <label>Contraseña</label>
                <input type="password" name="clave" placeholder="••••" required>

                <button type="submit" name="login">Ingresar</button>
            </form>
        </div>

    <?php } else { ?>

        <?php if ($_SESSION['usuario']['rol'] == "admin") { ?>
            <div class="card" style="border-top: 5px solid #1b4332;">
                <h2>🛡️ Panel de Administración</h2>
                <p>Bienvenido a la gestión central de la plataforma. Aquí puedes añadir fincas asociadas, revisar el stock de los productos e inspeccionar las alertas generales del sistema.</p>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                
                <h3>Añadir Nueva Granja (Simulado)</h3>
                <form method="post" action="" onsubmit="alert('Función de guardado en desarrollo en BD'); return false;">
                    <label>Nombre de la Granja</label>
                    <input type="text" placeholder="Ej. Finca Los Girasoles" required>
                    <label>Ubicación</label>
                    <input type="text" placeholder="Ej. Ahuachapán" required>
                    <button type="submit">Registrar Establecimiento</button>
                </form>
            </div>

        <?php } else { ?>
            
            <div class="card">
                <h2>🛒 Carrito de Compras</h2>
                <?php
                $total = 0;
                if (count($_SESSION['carrito']) > 0) {
                    foreach ($_SESSION['carrito'] as $item) {
                        echo "<p style='display:flex; justify-content:space-between;'>";
                        echo "<span>" . htmlspecialchars($item['nombre']) . "</span>";
                        echo "<b>$" . number_format($item['precio'], 2) . "</b>";
                        echo "</p>";
                        $total += $item['precio'];
                    }
                    echo "<hr style='border:0; border-top: 1px solid #ddd;'>";
                    echo "<h3 style='text-align:right;'>Total: $" . number_format($total, 2) . "</h3>";
                    ?>
                    <form method="post" style="text-align: right;">
                        <button type="submit" name="pedir" style="width: 100%; max-width: 200px;">Confirmar Pedido</button>
                    </form>
                    <?php
                } else {
                    echo "<p style='color: #666; italic;'>No hay productos en el carrito actualmente.</p>";
                }
                ?>
            </div>

            <h2>Granjas Disponibles</h2>
            <?php foreach ($granjas as $g) { ?>
                <div class="card">
                    <h3>🌾 <?php echo htmlspecialchars($g['nombre']); ?></h3>
                    <p style="color: #666; font-size: 0.9rem;">📍 <?php echo htmlspecialchars($g['ubicacion']); ?></p>
                    <p><?php echo htmlspecialchars($g['descripcion']); ?></p>

                    <h4>Productos Disponibles</h4>
                    <?php foreach ($g['productos'] as $p) { ?>
                        <div class="producto">
                            <div>
                                <b><?php echo htmlspecialchars($p['nombre']); ?></b><br>
                                <span style="color:#2d6a4f; font-weight:bold;">$<?php echo number_format($p['precio'], 2); ?></span>
                            </div>
                            <form method="post">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $p['precio']; ?>">
                                <button type="submit" name="agregar_carrito">Agregar</button>
                            </form>
                        </div>
                    <?php } ?>

                    <h4 style="margin-top:20px;">Dejar Reseña</h4>
                    <form method="post">
                        <input type="hidden" name="granja_nombre" value="<?php echo htmlspecialchars($g['nombre']); ?>">
                        <textarea name="comentario" placeholder="Escribe tu opinión sobre esta granja..." required></textarea>
                        <button type="submit" name="enviar_resena" style="background:#555;">Enviar Reseña</button>
                    </form>
                </div>
            <?php } ?>

        <?php } ?> <?php } ?> </div>

</body>
</html>