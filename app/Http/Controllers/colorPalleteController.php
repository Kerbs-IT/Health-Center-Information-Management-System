<?php

namespace App\Http\Controllers;

use App\Models\color_pallete;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class colorPalleteController extends Controller
{
    public function getInfo(){
        $colorPallete = color_pallete::where('id',1) -> first();

        return response() -> json(['primaryColor' => $colorPallete -> primaryColor,
                                    'secondaryColor' => $colorPallete -> secondaryColor,
                                'tertiaryColor' => $colorPallete -> tertiaryColor]);
    }

    public function updateInfo(Request $request){

        try{
            $data = $request->validate([
                'primaryColor' => 'required',
                'secondaryColor' => 'required',
                'tertiaryColor' => 'required'
            ]);

            $currentColorPallete = color_pallete::first();

            $currentColorPallete->update($data);

            return response()->json(['success' => 'updating color-pallete completed']);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
        

        
    }
}
