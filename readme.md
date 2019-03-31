![Banner](.images/banner.png)

# Test Eloquent ORM 

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
use Christophe\Customer as Customer;
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

## Author

Christophe Avonture
