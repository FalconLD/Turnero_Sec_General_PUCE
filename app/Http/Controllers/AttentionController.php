<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttentionController extends Controller
{
    public function index()
    {
        // Aquí puedes cargar datos si los necesitas para la vista
        // $data = SomeModel::all();

        return view('attention.index'); // Esto le dice que cargue la vista resources/views/attention/index.blade.php
    }
}
