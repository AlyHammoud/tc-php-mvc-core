<?php

namespace Alimvc\PhpMvc;

use Alimvc\PhpMvc\Db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName() : string;
}