<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:29 PM
 */
namespace APP\Entity;

use CORE\Entity;

/**
 * Class User
 * @package APP\Entity
 */
class User extends Entity
{
    public $id;
    public $name;
    public $balance;
}