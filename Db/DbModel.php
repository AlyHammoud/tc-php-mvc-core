<?php

namespace Alimvc\PhpMvc\Db;

use Alimvc\PhpMvc\Application;
use Alimvc\PhpMvc\Model;

abstract class DbModel extends Model
{
    abstract public function tableName() : string;
    abstract public function attributes() : array; //only for data to insert, since not all columns are required
    abstract public function primaryKey() : string; //only for data to insert, since not all columns are required

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName 
                                            (".implode(",", $attributes).") 
                                            VALUES 
                                            (".implode(',', $params).")");

        foreach ($attributes as $attribute){
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public function findOne(array $where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr" , $attributes));
        $stm = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $stm->bindValue(":$key", $item);
        }
        $stm->execute();
        return $stm->fetchObject(static::class);
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}