<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\ApiAuthmiddelware;
use App\Models\posts;
use app\Helpers\jwtAuth;
use Hamcrest\Type\IsObject;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class postcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(ApiAuthmiddelware::class, ['except' => ['index', 'show', 'getimage', 'obtenerPostPorCategoria', 'obtenerPostPorUsuarios']]);
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

            
            //consegir usuario identificado
            $user = $this->getidentity($res);

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
        

        if (!empty($params_array)) {
            //validar datos
            $validate = Validator::make($params_array, [
                'Titulo' => 'required',
                'content' => 'required',
                'categoria_id' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }
            //eliminar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);
            //conseguir usuario identificado
            $user = $this->getidentity($res);
            //busca el registro
            $post = posts::where('id',$id)->where('user_id', $user->sub)->first();
            if(!empty($post)&& is_object($post)) {
                $post->update($params_array);
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'eroor',
                'message' => 'llene los campos que desea actualizar '
            ];
        }
        //devolver datos
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $res)
    {
        //consegir usuario identificado
        $user = $this->getidentity($res);

        //conseguir el pst
        $post = posts::where('id', $id)->where('user_id', $user->sub)->first();
        if (!empty($post)) {

            //borrar registro
            $post->delete();
            //devolver algo
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post,
                'message' => 'registro eliminado correctamente'
            ];
        } else {

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Este post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function getidentity(Request $res)
    {
        //conseguir el usuario identificado
        $jwtAuth = new jwtAuth();
        $token = $res->header('Authorization', null);
        $user = $jwtAuth->checktoken($token, true);

        return $user;
    }


    public function upload(Request $res){
        //recoger la imagen de la peticion
        $image = $res->file('file0');
        //validar imagen
        $validate = Validator::make($res->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        //guardar imagen 
        if(!$image || $validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'

            ];

        }else{
            $imagen_name = time().$image->getClientOriginalName();
            Storage::disk('imagenes')->put($imagen_name, file::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Imagen subida correctamente',
                'img' => $imagen_name

            ];
        }
        //devolver datos
        return response()->json($data,$data['code']);

    }

    public function getimage($filename){
        //comprobar si existe la imagen 
        $isset = Storage::disk('imagenes')->exists($filename);

        if($isset){
            
            //conseguir la imagen
            $file = Storage::disk('imagenes')->GET($filename);

            //devolver la imagen 
            return new Response($file, 200);
        }else{
            //posibles errores
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe ese archvio'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function obtenerPostPorCategoria($id){
        $post = posts::where('categoria_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'post' => $post

        ], 200);
    }

    public function obtenerPostPorUsuarios($id){
        $posts = posts::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ]);

    }
}
