<?php

$user = App\User::find(1);

foreach ($user->roles as $role) {
    echo $role->pivot->created_at;
}
