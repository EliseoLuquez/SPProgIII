<?php
namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MateriaMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {       
        //$response = $handler->handle($request);
        $body = $request->getParsedBody();
        if(isset($body['materia']) && isset($body['cuatrimestre'])  && isset($body['cuatrimestre']))
        {
            if($body['materia'] != '' && $body['cuatrimestre']  != '' && $body['cuatrimestre']  != '')
            {
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                $resp = new Response();
                $resp->getBody()->write($existingContent); 
                return $resp; 
            }
            else 
            {
                $response = new Response();
                $response->getBody()->write('Faltan Completar Datos para Materia');
                $response->withStatus(403);
                return $response;
            }
        }
        else
        {
            $response = new Response();
            $response->getBody()->write('Faltan Valores para Materia');
            $response->withStatus(403);
            return $response;
        }
        
       // $response->getBody()->write('BEFORE ' . $existingContent);//
    }
}
