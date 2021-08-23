<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');

        // Use the next one when the role_user table contains timestamps
        // return $this->belongsToMany('App\Role')->withTimestamps();

    }
}
