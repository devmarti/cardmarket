<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    //
    public function registro(Request $request){

        $request -> validate([
            "nombre" => "required|string|unique:user",
            "email" => "required|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6}|unique:user",
            "contraseña" => "required|string|min:6",
            "rol" => "required|string"
        ]);

        $user = new User([
            "nombre" => $request->nombre,
            "email" => $request->email,
            "contraseña" => Hash::make($request->contraseña),
            "rol" => $request->rol
        ]);
        $user->save();
        return response()->json([$user]);
    }

    public function login(Request $request){

        $data = $request->getContent();
        $data = json_decode($data);
        $user = User::where('nombre', $data->nombre)->first();

        try{

            if($user){
                if(Hash::check($data->password, $user->password)){

                    do{
                        $token = Hash::make($user->id.now());
                    }while(User::where('token', $token)->first());

                    $user->token = $token;
                    $user->save();
                    $response['msg'] = "Login exitoso, el token de sesion es ".$user->api_token;

                }else{
                    $response['msg'] = "Credenciales no válidas.";
                }
            }else{
                $response['msg'] = "Este usuario no existe en nuestra base de datos.";
            }

        }catch(\Exception $e){
            $response['msg'] = "Error: ".$e->getMessage();
        }
         return response()->json($response);
    }

    public function recuperarContraseña(Request $request){
        $data = $request->getContent();
        $data = json_decode($data);
        $user = User::where('email', $data->email)->first();
        try{
            if($user){
                $user->token = null;
                $caracteres = "0123456789QWERTYUIOPÑLKJHGFDSAZXCVBNMmnbvcxzasdfghjklñpoiuytrewq";
                $caracteresLength = strlen($caracteres);
                $contraseñaNueva = '';
                for ($i=0; $i < 6; $i++) {
                    $contraseñaNueva .= $caracteres[rand(0, $caracteresLength - 1)];
                }
                $user->password = Hash::make($contraseñaNueva);
                $user->save();
                $response['msg'] = "Su nueva contraseña es ".$contraseñaNueva;

            }else{
                $response['msg'] = "Este usuario no existe en nuestra base de datos.";
            }

        }catch(\Exception $e){
            $response['msg'] = "Error: ".$e->getMessage();
        }

        return response()->json($response);
    }
}
