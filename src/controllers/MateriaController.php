<?php

namespace App\Controllers;

use Config\DataBase as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\UsuarioController;
use App\Models\Materia;
use App\Models\Usuario;
use App\Models\Inscripcion;
use App\Utils\ValidadorJWT;



class MateriaController {

    public function getAllMaterias(Request $request, Response $response, $args)
    {
        $rta = json_encode(Mascota::all());

        // $response->getBody()->write("Controller");
        $response->getBody()->write($rta);

        return $response;
    }

    public function addMateria(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        //$date = date('Y/m/d',strtotime($body["fecha_nac"]));

        $id = UsuarioController::TrerId();
        $usuario = Usuario::where('usuarios.id', $id)->first();

        if($usuario->tipo == 3)
        {
            $materia = new Materia;
    
            $materia->nombre = $body["materia"];
            $materia->cuatrimestre =  $body["cuatrimestre"];
            $materia->cupos= $body["cupos"];
    
            $rta = json_encode(array("ok" => $materia->save()));
        }
        else
        {
            $rta = json_encode(array("Error" => "Debe ser Admin"));
        }


        $response->getBody()->write($rta);

        return $response;
    }


    public function login(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $email = $body["email"];
        $clave = $body['password'];

         $usuarios = Funciones::TraerTodos();
         $usuario = Funciones::TraerUno($usuarios, $email);
         $valido = Funciones::ValidarLogin($usuario, $email, $clave);

        // $usuario = User::where('email', $email)->get();
        // var_dump($usuario->attributes['password']);
        if($valido)
        {
             $token = ValidadorJWT::CrearToken($usuario);
             $rta = json_encode($token);

        }
        else
        {
            $rta = json_encode("Error, usuario o clave no coinciden");
        }

        $response->getBody()->write($rta);

        return $response;
    }
}