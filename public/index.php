<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/db.php';

$app = AppFactory::create();

// Se detecta automáticamente la carpeta base donde está corriendo la API.
// En XAMPP será /APICR23036/public y en Render normalmente será vacío.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = str_replace('/index.php', '', $scriptName);

if ($basePath !== '') {
    $app->setBasePath($basePath);
}

$app->addBodyParsingMiddleware();

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST');
});

$app->get('/', function (Request $request, Response $response) {
    $data = [
        "mensaje" => "API REST CR23036 CLAVE 2 funcionando correctamente"
    ];

    $response->getBody()->write(json_encode($data));
    return $response;
});

/*
|--------------------------------------------------------------------------
| ENDPOINT: DOCTORES
|--------------------------------------------------------------------------
| GET  /doctores     -> Lista todos los doctores
| POST /doctores     -> Agrega un nuevo doctor
|--------------------------------------------------------------------------
*/

$app->get('/doctores', function (Request $request, Response $response) {
    try {
        $sql = "SELECT 
                    d.IdDoctor,
                    d.NombresDoctor,
                    d.ApellidosDoctor,
                    d.Especialidad,
                    d.TurnoAtencion,
                    d.PacientesMinDiarios,
                    d.Sueldo,
                    d.IdHospital,
                    h.NomHospital
                FROM Doctores d
                INNER JOIN Hospitales h ON d.IdHospital = h.IdHospital";

        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $doctores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($doctores));
        return $response;

    } catch (PDOException $e) {
        $error = [
            "success" => false,
            "mensaje" => "Error al obtener doctores",
            "detalle" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500);
    }
});

$app->post('/doctores', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();

        $sql = "INSERT INTO Doctores 
                (
                    IdDoctor,
                    NombresDoctor,
                    ApellidosDoctor,
                    Especialidad,
                    TurnoAtencion,
                    PacientesMinDiarios,
                    Sueldo,
                    IdHospital
                )
                VALUES
                (
                    :IdDoctor,
                    :NombresDoctor,
                    :ApellidosDoctor,
                    :Especialidad,
                    :TurnoAtencion,
                    :PacientesMinDiarios,
                    :Sueldo,
                    :IdHospital
                )";

        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':IdDoctor', $data['IdDoctor']);
        $stmt->bindParam(':NombresDoctor', $data['NombresDoctor']);
        $stmt->bindParam(':ApellidosDoctor', $data['ApellidosDoctor']);
        $stmt->bindParam(':Especialidad', $data['Especialidad']);
        $stmt->bindParam(':TurnoAtencion', $data['TurnoAtencion']);
        $stmt->bindParam(':PacientesMinDiarios', $data['PacientesMinDiarios']);
        $stmt->bindParam(':Sueldo', $data['Sueldo']);
        $stmt->bindParam(':IdHospital', $data['IdHospital']);

        $stmt->execute();

        $resultado = [
            "success" => true,
            "mensaje" => "Doctor agregado correctamente"
        ];

        $response->getBody()->write(json_encode($resultado));
        return $response;

    } catch (PDOException $e) {
        $error = [
            "success" => false,
            "mensaje" => "Error al agregar doctor",
            "detalle" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500);
    }
});

/*
|--------------------------------------------------------------------------
| ENDPOINT: HOSPITALES
|--------------------------------------------------------------------------
| GET  /hospitales?IdHospital=H001  -> Busca un hospital específico
| POST /hospitales                  -> Agrega un nuevo hospital
|--------------------------------------------------------------------------
*/

$app->get('/hospitales', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();

        if (!isset($params['IdHospital'])) {
            $resultado = [
                "success" => false,
                "mensaje" => "Debe enviar el parámetro IdHospital"
            ];

            $response->getBody()->write(json_encode($resultado));
            return $response->withStatus(400);
        }

        $idHospital = $params['IdHospital'];

        $sql = "SELECT 
                    IdHospital,
                    NomHospital,
                    CapacidadAtencion,
                    Especialidades
                FROM Hospitales
                WHERE IdHospital = :IdHospital";

        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':IdHospital', $idHospital);
        $stmt->execute();

        $hospital = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hospital) {
            $response->getBody()->write(json_encode($hospital));
            return $response;
        } else {
            $resultado = [
                "success" => false,
                "mensaje" => "Hospital no encontrado"
            ];

            $response->getBody()->write(json_encode($resultado));
            return $response->withStatus(404);
        }

    } catch (PDOException $e) {
        $error = [
            "success" => false,
            "mensaje" => "Error al buscar hospital",
            "detalle" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500);
    }
});

$app->post('/hospitales', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();

        $sql = "INSERT INTO Hospitales
                (
                    IdHospital,
                    NomHospital,
                    CapacidadAtencion,
                    Especialidades
                )
                VALUES
                (
                    :IdHospital,
                    :NomHospital,
                    :CapacidadAtencion,
                    :Especialidades
                )";

        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':IdHospital', $data['IdHospital']);
        $stmt->bindParam(':NomHospital', $data['NomHospital']);
        $stmt->bindParam(':CapacidadAtencion', $data['CapacidadAtencion']);
        $stmt->bindParam(':Especialidades', $data['Especialidades']);

        $stmt->execute();

        $resultado = [
            "success" => true,
            "mensaje" => "Hospital agregado correctamente"
        ];

        $response->getBody()->write(json_encode($resultado));
        return $response;

    } catch (PDOException $e) {
        $error = [
            "success" => false,
            "mensaje" => "Error al agregar hospital",
            "detalle" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500);
    }
});

$app->run();