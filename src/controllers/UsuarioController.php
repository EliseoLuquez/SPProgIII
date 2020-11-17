<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Usuario;
use App\Utils\ValidadorJWT;

class UsuarioController {

    public function getAll($request, $response, $args)
    {
        $rta = Usuario::all();

        $response->getBody()->write(json_encode($rta));

        return $response;
    }

    public function add($request, $response, $args)
    {
        $body = $request->getParsedBody();

        if(strlen($body["clave"]) >= 4)
        {
            $pass = password_hash($body["clave"], PASSWORD_DEFAULT);
            $usuario = new Usuario;
            $usuario->nombre = $body["nombre"];
            $usuario->email = $body["email"];
            $usuario->tipo = $body["tipo"];
            $usuario->clave = $pass;
            $rta = json_encode(array("ok" => $usuario->save()));
        }
        else
        {
            $rta = json_encode("Error, la clave debe tener al menos 4 caracteres");
        }
        $response->getBody()->write($rta);

        return $response->withHeader('Content-Type', 'application/json');;
    }



    public function login($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $email = $body["email"];
        $clave = $body['clave'];

         $usuario = UsuarioController::TraerUnUsuario($email);
         $valido = UsuarioController::ValidarLogin($usuario, $email, $clave);

        if($valido)
        {
             $token = ValidadorJWT::CrearToken($usuario);
             $rta = json_encode(array("token" => $token));
        }
        else
        {
            $rta = json_encode("Error, usuario o clave incorrectos");
        }

        $response->getBody()->write($rta);

        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function TraerUnUsuario($dato)
    {
        $usuario = Usuario::where('email', $dato)->first();
        
        return $usuario;
    }

    public static function TraerUnUsuarioNombre($dato)
    {
        $usuario = Usuario::where('nombre', $dato)->first();
        
        return $usuario;
    }

    public static function TrerId()
    {
        $header = getallheaders();
        $token = $header['token'];

        $usuario = ValidadorJWT::ObtenerUsuario($token);
        $dbUsuario = Usuario::where('email', $usuario->email)->first();

        return $dbUsuario->id;
    }

    public static function ValidarLogin($usuario, $email, $clave)
    {
        $rta = false;
        $pass = password_verify($clave, $usuario->clave);
        if($usuario->email == $email && $pass)
        {
            $rta = true;
        }

        return $rta;
    }

    public function getUsuario($request, $response, $args)
    {
        $usuario = UsuarioController::TraerUnUsuario($args['email']);
        //var_dump(unserialize($usuario->foto));
        unserialize($usuario->foto);

        $response->getBody()->write(json_encode($usuario));
        return $response;
    }

}