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
