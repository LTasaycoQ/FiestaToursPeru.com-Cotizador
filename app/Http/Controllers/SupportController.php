<?php

namespace App\Http\Controllers;

use App\Mail\SystemSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{

public function sendMessage(Request $request)
{
    $validated = $request->validate([
        'mensaje' => 'required|string|min:10|max:1000',
        'email'   => 'required|email', 
    ]);

    try {
        $user = auth()->user();

        Mail::to('dw@fiestatoursperu.com')
            ->cc(['luistasayco3030@gmail.com', 'dw1@fiestatoursperu.com'])
            ->send(new SystemSupport(
                $validated['mensaje'],
                $user->name,      
                $validated['email'] 
            ));

        return response()->json([
            'success' => true,
            'message' => 'Mensaje enviado correctamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
        ], 500);
    }
}
}