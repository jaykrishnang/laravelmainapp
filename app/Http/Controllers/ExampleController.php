<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage() 
    {
        $name = 'Brad';
        $catname = 'Jim';
        $animals = ['Cat', 'Dog', 'Buffalo'];
        return view('homepage', ['name' => $name, 'catname' => $catname, 'animals' => $animals]);
    }

    public function aboutpage() 
    {
        return view('single-post');
    }
}
