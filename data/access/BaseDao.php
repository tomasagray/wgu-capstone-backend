<?php
namespace Capstone;


use PDO;

interface BaseDao
{
    function __construct(PDO $db);
    function load($id);
    function save($object);
}