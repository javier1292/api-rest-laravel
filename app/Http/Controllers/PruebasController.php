<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\posts;
use App\Models\Categoria;

class PruebasController extends Controller
{
    public function testorm()
    {


        /*  $post = posts::all();
        foreach ($post as $posts) {
            echo "<h1>" . $posts->tutilo;
            echo "<h4>" . $posts->content;
            echo"<br>";
            echo "<span>{$posts->users->Nombre}- {$posts->Categoria->Nombre} </span>";
            echo "<hr>";
        }
        die(); */

        $cat = Categoria::all();
        foreach ($cat as $cats) {
            echo "<h1>" . $cats->Nombre;

            foreach ($cats-> posts as $post) {
                echo "<h3>" . $post->tutilo;
                echo "<h4>" . $post->content;
                echo "<br>";
                echo "<span>{$post->users->Nombre}</span>";
            }
            echo "<hr>";
        }
        die();
    }
}
