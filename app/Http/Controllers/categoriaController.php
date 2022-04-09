<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuthmiddelware;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class categoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware(ApiAuthmiddelware::class, ['except' => ['index', 'show']]);
    }

    //
    public function index()
    {
        $categories = Categoria::all();


        return Response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories

        ]);
    }

    public function show($id)
    {
        $category = Categoria::find($id);

        if (is_object($category)) {

            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category

            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'categoria no encontrado'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $res)
    {
        //recoger los datos por post
        $json = $res->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            //valida los datos 

            $validate = validator::make($params_array, [
                'Nombre' => 'required'
            ]);
            //guardar la categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'la categoria no se guardo'
                ];
            } else {
                $categories = new Categoria();
                $categories->Nombre = $params_array['Nombre'];
                $categories->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'se guardo correctamente '
                ];
            }
            //devolver el resultado 
            return response()->json($data, $data['code']);
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'LLene los campos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $res)
    {
        //recoger los paramentros por post 

        $json = $res->input('json', null);
        $params_array = json_decode($json, true);
        if (!empty($params_array)) {
            //validar los datos
            $validate = Validator::make($params_array, [
                'Nombre' => 'required'
            ]);
            //quitar lo que no quiero actualizar 
            unset($params_array['id']);
            unset($params_array['created_at']);

            //actualizar el registro 
            $categories = Categoria::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'datos actualizados correctamente ',
                'categria' => $params_array
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se envio ninguan categoria '
            ];
        }
        //devolver los datos 
        return response()->json($data, $data['code']);
    }
}
