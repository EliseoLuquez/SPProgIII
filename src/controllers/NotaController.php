<?php

namespace App\Controllers;

use Config\DataBase as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\InscripcionController;
use App\Models\Nota;
use App\Models\Turno;
use App\Models\Usuario;
use App\Utils\ValidadorJWT;
use App\Utils\Funciones;
use Illuminate\Contracts\Validation\Validator;

class NotaController {


    public function addNota(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $header = getallheaders();
        $body = $request->getParsedBody();

       // $notas = Nota::where('notas.id', $args['id_alumno'])->first();
        $id = UsuarioController::TrerId();
        //var_dump($id);
        $prof = InscripcionController::EsProfesor($id);

        //$usuario = ValidadorJWT::ObtenerUsuario($header['token']);

        if($prof)
        {
            $nota = new Nota();
            $nota->nota = $body["nota"];
            $nota->id_alumno = $body["idAlumno"];
            $rta = json_encode(array("ok" => $nota->save()));
        }
        else
        {
            $rta = json_encode("Solo el Prof puede agregar Notas");
        }

        $response->getBody()->write($rta);

        return $response;
    }


}