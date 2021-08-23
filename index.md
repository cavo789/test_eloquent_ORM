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
<!-- concat-md::include "./files/composer.json" -->
```

### Define our Database class

Create a `src` folder and create the `src/Database.php` file.

```php
<!-- concat-md::include "./files/src/Database.php" -->

```

### Define our customer class

Create an `src/Customer.php` file in the `src` folder (create it) with:

```php
<!-- concat-md::include "./files/src/Customer.php" -->
```

### And create our index file to test the solution

And finally create an `index.php` file with

```php
<!-- concat-md::include "./files/src/index.php" -->
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
<!-- concat-md::include "./files/hasOne/UserHasOnePhone.php" -->
```

File `~/App/Phone.php`

```php
<!-- concat-md::include "./files/hasOne/PhoneBelongsToUser.php" -->
```

### One to Many : hasMany - belongsTo

> [https://laravel.com/docs/7.x/eloquent-relationships#one-to-many](https://laravel.com/docs/7.x/eloquent-relationships#one-to-many)

A blog `Post` can have multiple `Comments` while a single comment belongs to one post. **The inverse of a hasOne relationship is the belongsTo method**.

File `~/App/PostHasManyComment.php`

```php
<!-- concat-md::include "./files/hasMany/PostHasManyComment.php" -->
```

File `~/App/Post.php`

```php
<!-- concat-md::include "./files/hasMany/CommentBelongsToPost.php" -->
```

### Many to Many

> [https://laravel.com/docs/7.x/eloquent-relationships#many-to-many](https://laravel.com/docs/7.x/eloquent-relationships#many-to-many)

Let's consider a list of users and a list of roles. A user can have multiple roles and a role can be shared by multiple users.

The `User 1` can have the `Admin` and `Super Admin` roles and the role `Admin` can be shared by `User 1` and `User 3`.

Working with a `many-to-many` relations requires the presence of an intermediate table. Eloquent will derive the name of the table using the singular names of the models. When models are `User` and `Role`, Eloquent will use, as intermediate table name, `role_user` (note, it's possible to use a custom model for that intermediate table: [https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models](https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models)).

```text
<!-- concat-md::include "./files/manyToMany/schemas.txt" -->
```

File `~/App/User.php`, the roles that belongs to the user:

```php
<!-- concat-md::include "./files/manyToMany/UserBelongsToManyRole.php" -->
```

File `~/App/Roles.php`, the users that belongs to the roles:

```php
<!-- concat-md::include "./files/manyToMany/RoleBelongsToManyUser.php" -->
```

After accessing this relationship, we may access the intermediate table using the pivot attribute on the models:

```php
<!-- concat-md::include "./files/manyToMany/userUsingPivot.php" -->
```


### Has one Through

> [https://laravel.com/docs/7.x/eloquent-relationships#has-one-through](https://laravel.com/docs/7.x/eloquent-relationships#has-one-through)

### Has many Through

> [https://laravel.com/docs/7.x/eloquent-relationships#has-many-through](https://laravel.com/docs/7.x/eloquent-relationships#has-many-through)


### Eager Loading 

> [https://laravel.com/docs/7.x/eloquent-relationships#eager-loading](https://laravel.com/docs/7.x/eloquent-relationships#eager-loading)

**Eager loading alleviates the N + 1 query problem.**

File `~/App/Book.php`: when access to f.i. a book, directly get the author. Using the `$with` public property allow to define which relations should be loaded in the same time. This will drastically reduce the number of queries to the database.

```php
<!-- concat-md::include "./files/eagerLoading/byDefault.php" -->
```

## Author

Christophe Avonture
