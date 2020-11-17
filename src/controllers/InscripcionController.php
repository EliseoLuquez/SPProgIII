<?php

namespace App\Controllers;

use Config\DataBase as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\UsuarioController;
use App\Controllers\InscripcionController;
use App\Models\Inscripcion;
use App\Models\Usuario;
use App\Models\Materia;
use App\Utils\ValidadorJWT;
use Symfony\Component\Console\Helper\Helper;

class InscripcionController {

    public function getAllTurnos($request, $response, $args)
    {
        $rta = json_encode(Turno::all());

        // $response->getBody()->write("Controller");
        $response->getBody()->write($rta);

        return $response;
    }

    public function addInscripcion($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $materia = Materia::where('materias.id', $args['id_materia'])->first();
        $id = UsuarioController::TrerId();
        //var_dump($id);
        $alumno = InscripcionController::EsAlumno($id);

        /*$cupos = Inscripcion::select('inscripcion.alumno_id', 'inscripcion.materia_id', 'materias.nombre')
                                ->where('materias.nombre', $materia->nombre)->groupBy('inscripcion.alumno_id', 'inscripcion.materia_id')
                                ->get();*/
        if($alumno)
        {
            if($materia !=  null && count($cupos) > 20)
            {
                $inscripcion = new Inscripcion;
                $inscripcion->alumno_id = $alumno->id;
                $inscripcion->materia_id = $args['id_materia'];
                $rta = json_encode(array("ok" => $inscripcion->save()));

            }
        }
        else
        {
            $rta = json_encode(array("error:" => "Debe ser Alumno"));
        }

        $response->getBody()->write($rta);

        return $response;
    }

    public function inscriptos($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $materia = Materia::where('materias.id', $args['id_materia'])->first();
        $id = UsuarioController::TrerId();

        $prof = InscripcionController::EsProfesor($id);
        $admin = InscripcionController::EsAdmin($id);

        if($prof || $admin)
        {
            $alumnos = Inscripcion::select('inscripcion.alumno_id', 'materias.nombre', 'usuarios.nombre', 'inscripcion.materia_id')
                //->whereDate('fecha', '2020-11-20')
                ->join('materias', 'materias.id', '=', 'inscripcion.materia_id')
                ->join('usuarios', 'usuarios.id', '=', 'usuarios.nombre')
                //->join('mascotas', 'mascotas.id', '=', 'turnos.mascota_id')
                //->join('usuarios as cliente', 'cliente.id', '=', 'mascotas.cliente_id')
            //->join('mascotas', 'mascotas.cliente_id', 'usuarios.id')
            ->get();
            //$insc = Inscripcion::where('materia_id', $args['id_materia'])->get();
            $rta = json_encode($alumnos);
        }
        else
        {
            $rta = json_encode("Debe ser Prof o Admin");
        }

        $response->getBody()->write($rta);

        return $response;
    }

    public static function TipoUsuario($id)
    {

        $usuario = Usuario::find($id);
        //var_dump($user);
        if($usuario->tipo  == 1)
        {
            $tipo = 1;
        }
        else if($usuario->tipo  == 2)
        {
            $tipo = 2;
        }
        return $tipo;
    }

    public static function EsAlumno($id)
    {
        //alumno tipo 1
        $retorno = false;
        $usuario = Usuario::find($id);
        if($usuario['tipo']  == 1)
        {
            $retorno = true;
        }
        return $retorno;
    }

    public static function EsProfesor($id)
    {
        //profesor tipo 2
        $retorno = false;
        $usuario = Usuario::find($id);
        if($usuario['tipo']  == 2)
        {
            $retorno = true;
        }
        return $retorno;
    }

    public static function EsAdmin($id)
    {
        //admin tipo 3
        $retorno = false;
        $usuario = Usuario::find($id);
        if($usuario['tipo']  == 3)
        {
            $retorno = true;
        }
        return $retorno;
    }

    public function id_usuario($request, $response, $args)
    {
       $usuario = Usuario::where('id', $args['id_usuario'])->first();
       //var_dump($args['id_usuario']);
       //var_dump($usuario->id);

       
       if($usuario != null)
       {
            $veterinario = TurnoController::EsVeterinario($usuario->id);
            $cliente = TurnoController::Escliente($usuario->id);
           if($veterinario)
           {
               /*
               $turnos = Turno::where('veterinario_id', $usuario->id)
                            ->whereDate('fecha', '2020-11-20')
                            ->join('usuarios', 'usuarios.id', '=', 'veterinario_id')
                            ->join('mascotas', 'mascotas.id', '=', 'mascota_id')
                            //->join('mascotas', 'mascotas.cliente_id', 'usuarios.id')
                            ->get();*/
                            $turnos = Turno::select('turnos.veterinario_id', 'turnos.fecha', 'turnos.mascota_id', 'mascotas.nombre', 
                            'usuarios.usuario as vet', 'cliente.usuario')
                                //->whereDate('fecha', '2020-11-20')
                                ->join('usuarios', 'usuarios.id', '=', 'turnos.veterinario_id')
                                ->join('mascotas', 'mascotas.id', '=', 'turnos.mascota_id')
                                ->join('usuarios as cliente', 'cliente.id', '=', 'mascotas.cliente_id')
                            //->join('mascotas', 'mascotas.cliente_id', 'usuarios.id')
                            ->get();

                            //var_dump($turnos[0]['cliente_id']);
                            //var_dump($turnos[1]['cliente_id']);
                //$dueño = Usuario::where('id', $turnos->cliente_id)->first();
                           //->whereDate('fecha', date("Y-m-d"))
                           //->join('usuarios', 'usuarios.id', '=', 'veterinario_id')
                           //->join('usuarios', 'usuarios.id', '=', 'turnos.veterinario_id')
                           //->join('mascotas', 'mascotas.id', '=', 'turnos.mascota_id')
                           //->join('usuarios', 'usuarios.id', '=', 'mascotas.cliente_id')
                           //var_dump($turnos);
            //$rta = array("Fecha:" => $turno->fecha, "Nombre:" => $turno->usuario, "Mascota:" => $turno->nombre, "Nacimiento:" => $turno->fecha_nacimiento);
               $rta = json_encode($turnos);
           }
           else if($cliente)
           {
               /*$mascotas = Mascota::where('cliente_id', $usuario->id)
                           //->whereDate('fecha', date("Y-m-d"))
                           ->join('usuarios' ,'usuarios.id', 'cliente_id')
                           ->join('turnos', 'turnos.mascota_id', 'mascotas.id')
                           //->join('usuarios', 'usuarios.id', 'turnos.veterinarios_id')
                           //->join('usuarios', 'usuarios.id', '=', 'turnos.veterinario_id')
                           //->join('mascotas', 'mascotas.id', '=', 'turnos.mascota_id')
                           ->get();*/

                           $mascotas = Turno::select('turnos.veterinario_id', 'turnos.fecha', 'turnos.mascota_id', 'mascotas.nombre', 
                           'usuarios.usuario as vet', 'cliente.usuario')
                               //->whereDate('fecha', '2020-11-20')
                               ->join('usuarios', 'usuarios.id', '=', 'turnos.veterinario_id')
                               ->join('mascotas', 'mascotas.id', '=', 'turnos.mascota_id')
                               ->join('usuarios as cliente', 'cliente.id', '=', 'mascotas.cliente_id')
                           //->join('mascotas', 'mascotas.cliente_id', 'usuarios.id')
                           ->get();
   
   
               //$turnos = Turno::join('turnos', '=', 'turnos.id_mascota')->get();
               //$turnos = Turno::where('id_veterinario', $id_usuario)->get();
               $rta = json_encode($mascotas);
           }
           else
           { 
               $rta = json_encode(array('error' => 'Tipo de usuario no valido'));
           }
       }
       else
       {
        $rta = json_encode(array('error' => 'Usuario Inexistente'));
       }

        $response->getBody()->write($rta);

        return $response;
    }

    public static function ValidarTurno($fecHr, $id_veterinario)
    {
        $retorno = false;
        $hrMin = array('horaMin'=> '09:00:00');
        $hrMax =  array('horaMax'=> '17:00:00');
     
        $date = explode(' ', $fecHr);
        $hora = $date[1];
        $fecha = $date[0];
        if($hrMin >= $hora && $hora < $hrMax)
        {
            $turno = Turno::select('veterinario_id', 'fecha')->where('fecha', $fecHr)->groupBy('veterinario_id', 'fecha')->get();
            //var_dump($turno);
            if(count($turno) == 1)
            {
                if($turno[0]['veterinario_id'] != $id_veterinario)
                {
                    var_dump(count($turno));
                    $retorno = true;
                }
            }
            else if(count($turno) == 0)
            {
                $retorno = true;
            }
        }
        return $retorno;
    }



}