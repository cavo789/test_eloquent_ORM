﻿<!-- This file has been generated by the concat-md.ps1 script. -->
<!-- Don't modify this file manually (you'll loose your changes) -->
<!-- but run the tool once more -->

<!-- Last refresh date: 2021-08-23 21:51:05 -->

<!-- below, content of ./Z:/home/christophe/repositories/test_eloquent_ORM/index.md -->

# Test Eloquent ORM

![Banner](./banner.svg)

> Test Eloquent outside Laravel and with a sqlite `in_memory` database

## Installation

* Get a copy of this repository
* Run `composer install` on command prompt

If you need to add a new class or change names, don't forget to
refresh composer autoload: run `composer dump-autoload` in the DOS
prompt.

## How this code was built; the story

Just create a new empty folder and go in it.

### Composer.json

Then run `composer require illuminate/database`; answer `no` to create
a new `composer.json` file (and not reusing an existing one).

Also run `composer require fzaninotto/faker` to install the `Faker`
library. So we'll be able to add dummy data like random name, random
firstname, random birthdate, ...

Finally open `composer.json` and add an autoloader for our classes:

```json
{
    "autoload": {
        "psr-4": {
            "Christophe\\": "src/"
        }
    },
    "require": {
        "illuminate/database": "^5.8",
        "fzaninotto/faker": "^1.8"
    }
}
```

### Define our Database class

Create a `src` folder and create the `src/Database.php` file.

```php
<?php

namespace Christophe;

use \Illuminate\Database\Capsule\Manager as DB;
use Christophe\Customer;
use Faker\Factory as Faker;

class Database
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $db = null;

    public function __construct()
    {
        $this->setUpDatabase();
        $this->migrateTables();
        $this->addDummyRecords();
    }

    /**
     * Just output the content of our table
     *
     * @return void
     */
    public function test()
    {
        echo PHP_EOL . 'Getting the list of customers' . PHP_EOL;
        echo '-----------------------------' . PHP_EOL . PHP_EOL;

        // Get all customers
        $customers = DB::table('customers')->get();
        print_r($customers);
    }

    /**
     * Create the database connection (in memory) and initialize
     * Eloquent.
     *
     * @return void
     */
    protected function setUpDatabase()
    {
        $this->db = new DB();

        // Define a "in-memory" database
        $this->db->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        // Required. Boot Laravel Eloquent
        $this->db->bootEloquent();

        // Make the database global
        $this->db->setAsGlobal();
    }

    /**
     * Create our `customers` table; just like we did in Laravel
     *
     * @return void
     */
    protected function migrateTables()
    {
        // Just in case the database wasn't in memory but on disk
        // In that case make sure the table isn't yet there
        DB::schema()->dropIfExists('customers');

        // Just like with Laravel migration up() function
        DB::schema()->create('customers', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('firstname');
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Add populate the table; rely on faker to get dummy records
     *
     * @return void
     */
    protected function addDummyRecords()
    {
        // use the factory to create a Faker\Generator instance
        $faker = Faker::create();

        for ($i=0; $i <= 100; $i++) {
            Customer::create([
                'name'      => $faker->name(),
                'firstname' => $faker->firstname(),
                'birthdate' => $faker->date(),
            ]);
        }
    }
}

```

### Define our customer class

Create an `src/Customer.php` file in the `src` folder (create it) with:

```php
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
```

### And create our index file to test the solution

And finally create an `index.php` file with

```php
<?php

require_once 'vendor/autoload.php';

use Christophe\Database;

$testDB = new Database();
$testDB->test();
```

## Run the test

Under DOS: `php index.php`

If everything goes fine, you'll get the list of records from the
database.

Remember, that database doesn't exists on disk, just in memory so,
when the script has finished to run, the database has been already
cleared from memory.

## Understand relations

### One to One: hasOne - belongsTo

> [https://laravel.com/docs/7.x/eloquent-relationships#one-to-one](https://laravel.com/docs/7.x/eloquent-relationships#one-to-one)

The `User` has one `Phone`, the phone belongs to a user. **The inverse of a hasOne relationship is the belongsTo method**.

File `~/App/User.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Get the phone record associated with the user.
     */
    public function phone()
    {
        return $this->hasOne('App\Phone');
    }
}
```

File `~/App/Phone.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
```

### One to Many : hasMany - belongsTo

> [https://laravel.com/docs/7.x/eloquent-relationships#one-to-many](https://laravel.com/docs/7.x/eloquent-relationships#one-to-many)

A blog `Post` can have multiple `Comments` while a single comment belongs to one post. **The inverse of a hasOne relationship is the belongsTo method**.

File `~/App/PostHasManyComment.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Get the comments for the blog post.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
}
```

File `~/App/Post.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
```

### Many to Many

> [https://laravel.com/docs/7.x/eloquent-relationships#many-to-many](https://laravel.com/docs/7.x/eloquent-relationships#many-to-many)

Let's consider a list of users and a list of roles. A user can have multiple roles and a role can be shared by multiple users.

The `User 1` can have the `Admin` and `Super Admin` roles and the role `Admin` can be shared by `User 1` and `User 3`.

Working with a `many-to-many` relations requires the presence of an intermediate table. Eloquent will derive the name of the table using the singular names of the models. When models are `User` and `Role`, Eloquent will use, as intermediate table name, `role_user` (note, it's possible to use a custom model for that intermediate table: [https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models](https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models)).

```text
users
    id - integer
    name - string

roles
    id - integer
    name - string

role_user
    user_id - integer
    role_id - integer
    created_at - timestamp
    updated_at - timestamp
```

File `~/App/User.php`, the roles that belongs to the user:

```php
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
```

File `~/App/Roles.php`, the users that belongs to the roles:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
```

After accessing this relationship, we may access the intermediate table using the pivot attribute on the models:

```php
<?php

$user = App\User::find(1);

foreach ($user->roles as $role) {
    echo $role->pivot->created_at;
}
```


### Has one Through

> [https://laravel.com/docs/7.x/eloquent-relationships#has-one-through](https://laravel.com/docs/7.x/eloquent-relationships#has-one-through)

### Has many Through

> [https://laravel.com/docs/7.x/eloquent-relationships#has-many-through](https://laravel.com/docs/7.x/eloquent-relationships#has-many-through)

## Author

Christophe Avonture
