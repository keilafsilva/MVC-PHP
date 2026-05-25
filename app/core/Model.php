<?php

namespace App\Core;

abstract class Model
{
    protected Database $db;

//contrutores no php não tem retorno, então não é necessário colocar void
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
