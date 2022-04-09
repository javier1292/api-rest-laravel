<?php

namespace App\Http\Controllers;

use app\Helpers\jwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use PhpParser\Node\Expr\Empty_;

class Usercontroller extends Controller
{
    public function pruebas(Request $res)
    {
        return "estas en usuarios";
    }




    //registro
    public function registo(Request $res)
    {
        //Recoger datos del usuario
        $json = $res->input('json', null);
        $params = json_decode($json, null); //objeto
        $params_array = json_decode($json, true); //array    
        if (!empty($params) && !empty($params_array)) {

            //Limpiar datos 
            $params_array = array_map('trim', $params_array);
            //validar datos
            $validate = Validator::make($params_array, [
                'Nombre' => 'required|alpha',
                'Apellido' => 'required|alpha',
                'Email' => 'required|email|unique:users',
                'Password' => 'required|'
            ]);

            if ($validate->fails()) {
                //validacion fallida
                $DatosU = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Usuario no encontrado',
                    'errors' => $validate->errors()
                );
            } else {
                //validacion pasada correctamente

                //cifrar contrseña
                $pwd = hash('sha256', $params->Password);
                //crear usuario
                $user = new User();
                $user->Nombre = $params_array['Nombre'];
                $user->Apellido = $params_array['Apellido'];
                $user->Email = $params_array['Email'];
                $user->password = $pwd;
                $user->Role = 'Usuario';
                //Guardar el usuario 
                $user->save();
                $DatosU = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario creado correctamente',
                );
            }
        } else {
            $DatosU = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Datos enviados no son correctos ',
            );
        }
        return response()->json($DatosU, $DatosU['code']);
    }

    //login

    public function Login(Request $res)
    {

        $jwtauth = new jwtAuth();

        //recibir datos por post 
        $json = $res->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //validar datos
        $validate = Validator::make($params_array, [

            'Email' => 'required|email',
            'Password' => 'required'
        ]);
        if ($validate->fails()) {
            //validacion fallida
            $singup = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Fatal error',
                'errors' => $validate->errors()
            );
        } else {


            //cifrar la contrseña
            $pwd = hash('sha256', $params->Password);

            //devolver token

            $singup = $jwtauth->singup($params->Email, $pwd);

            if (!empty($params->gettoken)) {
                $singup = $jwtauth->singup($params->Email, $pwd, true);
            }
        }



        return response()->json($singup, 200);
    }

    //actualizar usuario 
    public function update(Request $res)
    {
        //COMPROBAR SI EL USUARIO ESTA IDENTIFICADO
        $token = $res->header('Authorization');
        $jwtauth = new jwtAuth();
        $checkToken = $jwtauth->checktoken($token);

        //RECOGER LOS DATOS POR POST
        $json = $res->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {


            //sacar usuario identificado
            $user = $jwtauth->checktoken($token, true);



            //VALIDAR DATOS
            $validate = Validator::make($params_array, [
                'Nombre' => 'required|alpha',
                'Apellido' => 'required|alpha',
                'Email' => 'required|email|unique:users,' . $user->sub

            ]);
            //QUITAR DATOS QUE NO QUIERO ACTUALIZAR
            unset($params_array['id']);
            unset($params_array['Role']);
            unset($params_array['Password']);
            unset($params_array['Create_at']);
            unset($params_array['Token']);

            //ACTUALIZAR LOS DATOS EN LA BD
            $user_update = User::where('id', $user->sub)->update($params_array);

            //DEVOLVER ARRAY
            $Data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            $Data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no se a identificado'
            );
        }

        return response()->json($Data, $Data['code']);
    }
    //Subir imagen

    public function upload(Request $res)
    {

        //recoger los datos de la peticion
        $imagen = $res->file('file0');


        //validaciond de la imagen 
        $validate = validator::make($res->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        //guardar imagen 
        if (!$imagen || $validate->fails()) {

            //devolver el resultado
            $Data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al pasar la  img'
            );
        } else {
            $imagen_nombre = time() . $imagen->getClientOriginalName();

            Storage::disk('users')->put($imagen_nombre, File::get($imagen));
            $Data = array(
                'code' => '200',
                'status' => 'success',

                'image' => $imagen_nombre
            );
        }

        return response()->json($Data, $Data['code']);
    }

    //obtener imagen
    public function GetImagen($filname)
    {
        $isset = Storage::disk('users')->exists($filname);
        if ($isset) {
            $file = Storage::disk('users')->get($filname);
            return new response($file, 200);
        } else {
            $Data = array(
                'code' => '200',
                'status' => 'success',

                'message' => 'Esta imagen no existe '
            );
            return response()->json($Data, $Data['code']);
        }
    }

    //detalles de la cuenta 
    public function detail($id)
    {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            );
        }
        return response()->json($data, $data['code']);
    }
}
