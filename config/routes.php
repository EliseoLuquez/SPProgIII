<?php

namespace Config;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsuarioController;
use App\Controllers\InscripcionController;
use App\Controllers\MateriaController;
use App\Controllers\NotaController;
use App\Middlewares\UsuarioValidateMiddleware;
use App\Middlewares\RegistroMiddleware;
use App\Middlewares\ExisteNombreMiddleware;
use App\Middlewares\ExisteEmailMiddleware;
use App\Middlewares\MateriaMiddleware;
use App\Middlewares\LoginMiddleware;
use App\Middlewares\TurnoMiddleware;

return function ($app){
        //PUNTO 1 
        $app->post('/users', UsuarioController::class . ':add')->add(ExisteEmailMiddleware::class)
                                                                ->add(ExisteNombreMiddleware::class)
                                                                ->add(RegistroMiddleware::class);
    
        //PUNTO 2
        $app->post('/login', UsuarioController::class . ':login')->add(LoginMiddleware::class);
                                                                
                                                                
        
        //PUINTO 3 
        $app->post('/materia', MateriaController::class . ':addMateria')->add(MateriaMiddleware::class)
                                                                                ->add(UsuarioValidateMiddleware::class);
       
        //$app->get('/usuario/{email}', UsuarioController::class . ':getUsuario');
        //PUINTO 4
        $app->post('/inscripcion/{id_materia}', InscripcionController::class . ':addInscripcion')->add(UsuarioValidateMiddleware::class);
    
        //PUNTO 5 
        $app->put('/notas/{id_materia}', NotaController::class . ':addNota')->add(UsuarioValidateMiddleware::class);

             
        //PUNTO 6
        $app->get('/inscripcion/{id_materia}', InscripcionController::class . ':inscriptos')->add(UsuarioValidateMiddleware::class);

        
        //$app->post('/turno', TurnoController::class . ':addTurno')->add(UsuarioValidateMiddleware::class);
    
        //PUNTO 7 ->add(ExisteServicioMiddleware::class)
        //$app->post('/stats[/{tipo}]', ServicioController::class . ':traerTipo')->add(UsuarioValidateMiddleware::class);
        

};