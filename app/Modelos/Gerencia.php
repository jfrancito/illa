<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Gerencia extends Model
{
    //
    protected $table = 'gerencias';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
}
