<?php

namespace App\Core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public function loadData($data) : void //store the coming data from the $_POST with the matching properties
    {
        foreach ($data as $key => $value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules() : array; //rules should be defined in the XModel having array or constants of rules needed

    public array $errors = [];

    public function validate()
    {
        /* Rules sample, iterate on each rule as array of array
         * return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3], [self::RULE_MAX, 'max' => 8]],
            'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
        ];
         */                         //email      [requ, email]
        foreach ($this->rules() as $attribute => $rules){
            if(property_exists($this, $attribute)){
                $value = $this->{$attribute}; //get the value of the properties set in User model// $this->{email} => value of the email we get it from this class = d@a.c //
            }else{
                echo "property does not exist";exit;
            }

            foreach ($rules as $rule){ //$rule set in rules() in ex: User model
                $ruleName = $rule; //self::RULE_REQUIRED = 'required'
                if(!is_string($ruleName)){ // ex: in password second rule is array, so the first element in the second array is the string [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3], [self::RULE_MAX, 'max' => 8]],
                    $ruleName = $rule[0];
                }
                if($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < (int)$rule['min']){
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > (int)$rule['max']){
                    $this->addErrorForRule($attribute,self::RULE_MAX, $rule);
                }

                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $this->addErrorForRule($attribute,self::RULE_MATCH, $rule);
                }

                if($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $stm = Application::$app->db->prepare("select * from $tableName where $uniqueAttr = :attr");
                    $stm->bindValue(":attr", $value);
                    $stm->execute();
                    $record = $stm->fetchObject();
                    if($record){
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabels($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public  function labels() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getLabels($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    private function addErrorForRule(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';

        foreach ($params as $key => $param) {
            $message = str_replace("{{$key}}", $param, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }


    public function errorMessages() : array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email',
            self::RULE_MIN => "Min length of this field must be {min}",
            self::RULE_MAX => "Max length of this field must be {max}",
            self::RULE_MATCH => "This field must be the same as {match}",
            self::RULE_UNIQUE => "This {field} already exists"
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}