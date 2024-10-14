<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Margenes extends Model
{
    protected $table = 'margenes';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
