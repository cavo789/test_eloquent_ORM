<?php

namespace Christophe;

use Illuminate\Database\Eloquent\Model as Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $id        = 0;
    protected $name      = '';
    protected $firstname = '';
    protected $birthdate = null;

    protected $fillable = ['name', 'firstname', 'birthdate'];
}
