<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
  protected $table = 'categorias';

  //relacion de uno a muchos

  public function posts()
  {
    return $this->hasMany('App\Models\posts');
  }
}
