<?php

namespace App\Helpers;

class Colour {

    public $r, $g, $b;

    public function toHex(){
        return '#' . self::formatToHex($this->r) . self::formatToHex($this->g) . self::formatToHex($this->b);
    }

    protected static function formatToHex($x){
        return str_pad(dechex(intval($x * 255)), 2, "0", STR_PAD_LEFT);
    }

    protected static function hue2rgb(float $f1, float $f2, float $hue){
        while($hue < 0.0) $hue += 1.0;
        while($hue >= 1.0) $hue -= 1.0;
        if((6.0 * $hue) < 1.0){
            $res = $f1 + ($f2 - $f1) * 6.0 * $hue;
        }else if((2.0 * $hue) < 1.0){
            $res = $f2;
        }else if ((3.0 * $hue) < 2.0){
            $res = $f1 + ($f2 - $f1) * ((2.0 / 3.0) - $hue) * 6.0;
        }else{
            $res = $f1;
        }
        return $res;
    }

    /**
     * @param float $hue between 0 and 1
     * @param float $sat between 0 and 1
     * @param float $val between 0 and 1
     * @return Colour
     */
    public static function FromHSL(float $hue, float $sat, float $val){
        $c = new Colour;
        if($sat <= 0){
            $c->r = $c->g = $c->b = $val;
        }else{
            if($val < 0.5){
                $f2 = $val * (1.0 + $sat);
            }else{
                $f2 = $val + $sat - $sat * $val;
            }
            $f1 = 2.0 * $val - $f2;
            $c->r = self::hue2rgb($f1, $f2, $hue + 1.0/3.0);
            $c->g = self::hue2rgb($f1, $f2, $hue);
            $c->b = self::hue2rgb($f1, $f2, $hue - 1.0/3.0);
        }
        return $c;
    }

}
