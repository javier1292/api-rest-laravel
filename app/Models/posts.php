<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

  class posts extends Model
{
   protected $table = 'posts';

   //RELACION DE MUCHOS A UNO 
   public function users(){
       return $this->belongsTo('App\Models\User', 'user_id');
   }

   public function categoria(){
       return $this->belongsTo('App\Models\Categoria','categoria_id');
   }
}
