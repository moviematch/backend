<?php

namespace App\Helpers;

use Hashids\Hashids;

class Id{

    protected static $__hashid_instance = null;

    protected static function hashid(){
        if(!Id::$__hashid_instance)
            Id::$__hashid_instance = new Hashids('Movie-Match-Ids', 4);
        return Id::$__hashid_instance;
    }

    public static function encode($id){
        
        if(is_array($id)){
            $id = $id['id'];
        }
        if(is_object($id)){
            $id = $id->id;
        }
        if(is_null($id)){
            return null;
        }

        $encoded = self::hashid()->encode($id);

        if($encoded == '') return null;

        return $encoded;
    }

    public static function decode($id){

        if(is_array($id)){
            $id = $id['id'];
        }
        if(is_object($id)){
            $id = $id->id;
        }
        if(is_null($id)){
            return null;
        }

        $decoded = self::hashid()->decode($id);
        if(count($decoded) <= 0) return null;
        $decoded = $decoded[0];

        if($decoded == null || $decoded == '') return null;

        return $decoded;
    }

    public static function encodeSeveral($ids){
        $encoded = [];
        foreach($ids as $id){
            $encoded[] = self::encode($id);
        }
        return $encoded;
    }

    public static function decodeSeveral($ids){
        $decoded = [];
        foreach($ids as $id){
            $decoded[] = self::decode($id);
        }
        return $decoded;
    }

    /**
     * Provides a validation rule to check that an input is a valid hashid.
     */
    public static function validateId($attribute, $value, $parameters, $validator){
        
        // check that the value is a valid hashid
        $decoded = self::decode($value);
        if(!is_int($decoded)) return false;

        // if a model name is provided, check that the id given corresponds to a valid row in the database
        if(count($parameters) > 0){
            $modelName = 'App\\Models\\' . $parameters[0];
            $row = call_user_func([$modelName, 'find'], $decoded); // call find() on the model with id $decoded
            if(!$row) return false;
        }

        return true;
    }

    /**
     * Provides a validation rule to check that an input is a valid array of hashids.
     */
    public static function validateIdArray($attribute, $value, $parameters, $validator){

        // check that the value is an array
        if(!is_array($value)) return false;

        // check that the value is a valid hashid collection
        $decoded = self::decodeSeveral($value);
        if(count($decoded) != count($value)) return false;

        // if a model name is provided, check that the ids given all correspond to valid rows in the database
        if(count($parameters) > 0){
            $modelName = 'App\\Models\\' . $parameters[0];

            // if providing parameter 'duplicates', allow inputting array with duplicate ids
            if(count($parameters) > 1 && $parameters[1] == 'duplicates'){
                $decoded = array_unique($decoded);// keep only unique ids to check against the database
            }

            $rows = call_user_func([$modelName, 'find'], $decoded); // call find() on the model with ids $decoded
            if(count($rows) != count($value)) return false;
        }

        return true;

    }

}
