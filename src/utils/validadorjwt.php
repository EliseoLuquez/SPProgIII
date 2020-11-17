<?php

namespace App\Utils;

use \Firebase\JWT\JWT;

class ValidadorJWT
{
    private static $claveSecerta = 'sparcial';
    private static $encriptacion = ['HS256'];
    private static $aud = null;

    public static function CrearToken($dato)
    {
        $retorno = false;
        $key = 'sparcial';
        $payload = array(
            "email" => $dato['email'],
            "tipo"=> $dato['tipo'],
            "clave"=> $dato['clave'],
        );
        $retorno = JWT::encode($payload, $key);
        return $retorno;
    }

    public static function VerificarToken($token)
    {
        $retorno = JWT::decode($token, 'sparcial', array('HS256'));
        return $retorno;
    }

    public static function ObtenerUsuario($token)
    {

        $retorno = JWT::decode($token, self::$claveSecerta, self::$encriptacion);
        
        return $retorno;
    }
    
}