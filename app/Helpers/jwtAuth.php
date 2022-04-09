<?php
namespace app\Helpers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Else_;

class jwtAuth{

    public $key;

    public function __construct()
    {
        $this->key = 'esta_es_la_key_20000';
    }
    
        
    

    public function singup($Email,$Password, $gettoken = null){

        //buscar credenciales existentes
        $user = User::where([
            'Email' => $Email,
            'Password' => $Password
        ])->first();

    
        //comprobar si las credenciales son correctas
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
    
        //Generar el token 
        if($signup){
            $token = array(
                'sub' => $user->id,
                'Email' => $user->Email,
                'Nombre' => $user->Nombre,
                'Apellido' => $user->Apellido,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );


            $jwt = JWT::encode($token,$this->key, 'HS256');
            $decoded = JWT::decode($jwt,$this->key, ['HS256']);
            
            //Devolver los datos decodificados 
            if(is_null($gettoken)){

                $data = $jwt;
                
            }else{
             
                $data = $decoded;
    
            }


        }else{
            $data =array(
                'status' => 'error',
                'message' => 'Login incorreto'
            );
        }
        return $data;
    }

    public function checktoken($jwt, $getIdentity = false){
        $auth = false;

    

        try{

            $jwt = str_replace('"','',$jwt);
            $decoded =JWT::decode($jwt, $this->key, ['HS256']);
        }Catch(\UnexpectedValueException $e){
            $auth = FALSE;
        }catch(\DomainException $e){
            $auth = false;
        }

        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;

        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;

        }

        return $auth;

    }

}