<?php
namespace Capstone;


use JsonSerializable;

interface PDOable extends JsonSerializable
{
    public function as_pdo_array();
    public function jsonSerialize();
}