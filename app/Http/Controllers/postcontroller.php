<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\ApiAuthmiddelware;
use App\Models\posts;
use app\Helpers\jwtAuth;
use Illuminate\Support\Facades\Validator;

class postcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(ApiAuthmiddelware::class, ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $posts = posts::all()->load('categoria');
        return response()->json([
            'code' => 200,
            'status' => 'succes',
            'posts' => $posts

        ]);
    }

    public function show($id)
    {
        $post = posts::find($id)->load('categoria');
        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'succes',
                'posts' => $post
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se a encontrado ningun post '
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $res)
    {
        //recoger datos por post
        $json = $res->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if (!empty($params_array)) {

            //conseguir el usuario identificado
            $jwtAuth = new jwtAuth();
            $token = $res->header('Authorization', null);
            $user = $jwtAuth->checktoken($token, true);
            //validar los datos 
            $validate = Validator::make($params_array, [
                'Titulo' => 'required',
                'content' => 'required',
                'categoria_id' => 'required',
                'imagen' => 'required'

            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'faltan datos'

                ];
            } else {
                //guardar el post
                $post = new posts();
                $post->user_id = $user->sub;
                $post->categoria_id = $params->categoria_id;
                $post->Titulo = $params->Titulo;
                $post->content = $params->content;
                $post->imagen = $params->imagen;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post

                ];
            }
        } else {

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Tienes que enviar los datos correctamente '

            ];
        }

        //devolver la repsuesta 
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $res)
    {
        //recoger datos por post 
        $json = $res->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            
            //validar datos
            $validate = Validator::make($params_array, [
                'Titulo' => 'required',
                'content' => 'required',
                'categoria_id' => 'required'
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }
            //eliminar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['User_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);
            //actualizar registro
            $post = posts::where('id', $id)->updateOrcreate($params_array);
            $data = [
                'code' => 200,
                'status' => 'success',
                'psot' => $post,
                'changes' => $params_array
                
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'eroor',
                'message' => 'llene los campos que desea actualizar '
                
            ];
        }
        
        //devolver datos
        return response()->json($data, $data['code']);
    }
}
