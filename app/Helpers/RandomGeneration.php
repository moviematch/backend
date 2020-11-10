<?php

namespace App\Helpers;

use App\Models\Word;
use Illuminate\Support\Str;

class RandomGeneration {

    /**
     * @return string random noun
     */
    public static function RandomNoun(){
        $word = Word::where('adjective', 0)->orderByRaw('RAND()')->first();
        if(!$word) return 'error';
        return $word->word;
    }

    /**
     * @return string random adjective
     */
    public static function RandomAdjective(){
        $word = Word::where('adjective', 1)->orderByRaw('RAND()')->first();
        if(!$word) return 'erroneous';
        return $word->word;
    }

    /**
     * @return string random name constructed from adjective + name
     */
    public static function RandomName(){
        $adjective = Str::title(self::RandomAdjective());
        $noun = Str::title(self::RandomNoun());
        return $adjective . ' ' . $noun;
    }

    /**
     * @return string random CSS colour
     */
    public static function RandomColour($minSat = 0.6, $maxSat = 0.8, $minLum = 0.5, $maxLum = 0.7){
        $h = mt_rand() / mt_getrandmax();
        $s = $minSat + mt_rand() / mt_getrandmax() * ($maxSat - $minSat);
        $l = $minLum + mt_rand() / mt_getrandmax() * ($maxLum - $minLum);
        return Colour::FromHSL($h, $s, $l)->toHex();
    }

    /**
     * @return string random string of given length and with given alphabet
     */
    public static function RandomString($length = 4, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $charactersLength = strlen($characters);
        $s = '';
        for ($i = 0; $i < $length; $i++) {
            $s .= $characters[rand(0, $charactersLength - 1)];
        }
        return $s;
    }

}
