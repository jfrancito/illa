<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Ilog extends Model
{
    protected $table = 'ilogs';
    public $timestamps=false;
    protected $primaryKey = 'id';
}
