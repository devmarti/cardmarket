<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carta;
use App\Models\Colección;
use App\Models\CartaColección;
use App\Models\Oferta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartasController extends Controller
{
    //
    public function subirCarta(Request $request){
        $request -> validate([
            "nombre" => "required|string",
            "descripción" => "required|text",
            "colección" => "required|integer"
        ]);

        if($request->fails()){
            $response["msg"] = $request->errors();
        }
        else{
            $data = $request->getContent();
            $data = json_decode($data);
            $colección = Colección::where('id',$data->colección)->first();

            if($colección){
                $carta = new Carta();
                $carta->nombre = $data->nombre;
                $carta->descripción = $data->descripción;
                try {
                    $carta->save();
                    $cartaColección = new CartaColección();
                    $cartaColección->carta_id = $carta->id;
                    $cartaColección->colección_id = $colección->id;
                    $cartaColección->save();
                    $response['msg'] ='Carta y cartaColección creadas correctamente.';
                } catch (\Exception $e) {
                    $response['msg'] = 'Error: '.$e->getMessage();
                }
            } else {
                $response['msg'] = 'Esa colección no existe en nuestra base de datos.';
            }
        }
        return response()->json($response);
    }

    public function subirColección(Request $request){
        $request -> validate([
            "nombre" => "required|string",
            "símbolo" => "required|string",
            "fecha_edición" => "required|date",
            "carta" => "required"
        ]);

        if($request->fails()){
            $response["msg"] = $request->errors();
        }
        else{
            $data = $request->getContent();
            $data = json_decode($data);
            $Id =[];
            foreach ($data->carta as $nuevaCarta) {
                if(isset($nuevaCarta->id)){
                    $carta = Carta::where('id',$nuevaCarta->id)->first();
                if($carta){
                    array_push($Id,$carta->id);
                }
                }elseif (isset($nuevaCarta->nombre) && isset($nuevaCarta->descripción)) {
                    $otraCarta = new Carta();
                    $otraCarta->nombre = $nuevaCarta->nombre;
                    $otraCarta->description = $nuevaCarta->descripción;
                    try {
                        $otraCarta->save();
                        array_push($Id,$otraCarta->id);
                        $response['msg'] ='Carta guardada correctamente';
                    } catch (\Exception $e) {
                        $response['msg'] ='Error: '.$e->getMessage();
                    }
                }else{
                    $response['msg'] ='Datos erróneos';
                }
        }

        if(!empty($Id)){

            try{
                $colección = new Colección();
                $colección->nombre = $data->nombre;
                $colección->símbolo = $data->símbolo;
                $colección->fecha_edición = $data->fecha_edición;
                $colección->save();

                foreach($Id as $id){
                    $cartaColección = new CartaColección();
                    $cartaColección->carta_id = $id;
                    $cartaColección->colección_id = $colección->id;
                    $cartaColección->save();
                }
                $response['msg'] ='Se ha creado la colección correctamente.';

            }catch (\Exception $e) {
                $response['msg'] ='Error: '.$e->getMessage();
            }

        }
        return response()->json($response);
        }
    }



    public function buscarCarta(Request $request){
        $request -> validate([
            "búsqueda" => "required"
        ]);

        if($request->fails()){
            $response["msg"] = $request->errors();
            Log::error($response);
        }
        else{
            try{
                $data = $request->getContent();
                $data = json_decode($data);

                $buscar = DB::table('cartas')
                            ->select(['id','nombre','descripción'])
                            ->where('nombre','like','%'. $data -> buscar.'%')
                            ->get();
                $response['msg'] = $buscar;
                Log::info($response);
            }catch(\Exception $e){
                $response['msg'] ='Error: '.$e->getMessage();
                Log::error($response);
            }
        }
        return response()->json($response);
    }

    public function crearOferta(Request $request){
        $request -> validate([
            "carta_id" => "required|integer",
            "cantidad" => "required|integer",
            "precio" => "required|numeric|min:0|not_in:0"
        ]);

        if($request->fails()){
            $response["msg"] = $request->errors();
        }
        else{
            $data =  $request->getContent();
            $data = json_decode($data);

            $carta = Carta::where('id',$data->card_id)->first();

            if($carta){
                $oferta = new Oferta();
                $oferta->carta_id = $data->carta_id;
                $oferta->cantidad = $data->cantidad;
                $oferta->precio = $data->precio;
                $oferta->vendedor = $request->user->id ;
                try {
                    $oferta->save();
                    $response['msg'] ='Oferta creada correctamente';
                } catch (\Exception $e) {
                    $response['msg'] ='Error: '.$e->getMessage();
                }

            }else{
                $response['msg'] ='Credenciales incorrectas.';
            }
        }
        return response()->json($response);
    }

    public function buscarOferta(Request $request){
        $request -> validate([
            "búsqueda" => "required"
        ]);

        if($request->fails()){
            $response["msg"] = $request->errors();
        }
        else{

            try{
                $data = $request->getContent();
                $data = json_decode($data);

                $buscar = DB::table('ofertas')
                            ->join('users', 'users.id', '=', 'ofertas.vendedor')
                            ->join('cartas', 'cartas.id', '=', 'ofertas.carta_id')
                            ->select('cartas.nombre', 'ofertas.cantidad', 'ofertas.precio', 'users.nombre as vendedor')
                            ->where('cartas.nombre','like','%'. $data -> buscar.'%')
                            ->orderBy('ofertas.precio','ASC')
                            ->get();
                $response['msg'] = $buscar;
            }catch(\Exception $e){
                $respuesta['msg'] ='Error: '.$e->getMessage();
            }
        }
        return response()->json($response);
    }

}




