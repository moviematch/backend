<?php

namespace App\Helpers;

class Image{

    public $resource;// underlying GD image resource

    /**
     * If the path given is a valid image file, return an Image object created from it
     * If not return null
     */
    public static function FromFile(string $path){
        try{
            return new self($path);
        }catch(\Exception $e){
            return null;
        }
    }

    public function __construct(string $url){
        // load image data
        $this->resource = imagecreatefrompng($url);
        if($this->resource === false){
            throw new \Exception('Cannot create image');
        }
    }

    /**
     * Crops the image contents to the given rectangle
     */
    public function crop(int $x, int $y, int $width, int $height){
        // crop image contents
        $this->resource = imagecrop($this->resource, [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        ]);
        if($this->resource === false){
            throw new \Exception('Cannot crop image');
        }
    }

    /**
     * Scales the image up or down
     */
    public function scale(float $factor = 0.5){
        // read width and height from image
        $width = imagesx($this->resource);
        $height = imagesy($this->resource);
        if($width === false || $height === false){
            throw new \Exception('Cannot read image size!');
        }
        // allocate new image with correct size
        $resized = imagecreatetruecolor($width * $factor, $height * $factor);
        if($resized === false){
            throw new \Exception('Cannot create resized image');
        }
        // copy contents to new image
        if(!imagecopyresampled($resized, $this->resource, 0, 0, 0, 0, $width * $factor, $height * $factor, $width, $height)){
            throw new \Exception('Failed to copy image contents');
        }
        $this->resource = $resized;
    }

    /**
     * Adds a coloured border around the image
     */
    public function addBorder(int $thickness, $colour){
        // ensure correctness of params
        if(is_string($colour)) $colour = intval($colour);
        if(is_int($colour)) $colour = [$colour];
        if(!is_array($colour)){
            throw new \Exception('$colour should be an array!');
        }
        if(count($colour) == 1) $colour = [$colour[0], $colour[0], $colour[0]];
        if(count($colour) != 3){
            throw new \Exception('$colour should an array of size 3!');
        }
        // read width and height from image
        $width = imagesx($this->resource);
        $height = imagesy($this->resource);
        if($width === false || $height === false){
            throw new \Exception('Cannot read image size!');
        }
        // allocate new image with space for border
        $withBorder = imagecreatetruecolor($width + 2 * $thickness, $height + 2 * $thickness);
        if($withBorder === false){
            throw new \Exception('Failed to create new image');
        }
        // fill new image
        $bgColour = imagecolorallocate($withBorder, $colour[0], $colour[1], $colour[2]);
        if($bgColour === false){
            throw new \Exception('Failed to allocate border colour');
        }
        if(!imagefilledrectangle($withBorder, 0, 0, $width + 2 * $thickness, $height + 2 * $thickness, $bgColour)){
            throw new \Exception('Failed to fill image with border colour');
        }
        // copy over initial image contents
        if(!imagecopyresized($withBorder, $this->resource, $thickness, $thickness, 0, 0, $width, $height, $width, $height)){
            throw new \Exception('Failed to copy image contents');
        }
        $this->resource = $withBorder;
    }

    /**
     * Saves the image to a png file
     */
    public function savePNG(string $path){
        $explodedPath = explode('/', $path);
        $directory = "";
        for($i = 0; $i < count($explodedPath) - 1; ++$i){
            if(strlen($directory) > 0) $directory .= '/';
            $directory .= $explodedPath[$i];
        }
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        if(!imagepng($this->resource, $path)){
            throw new \Exception('Failed to write PNG image');
        }
    }

    /**
     * Return output PNG stream with Content-Type image/png
     */
    public function responsePNG(){
        if(!ob_start()){
            throw new \Exception('Failed to open output buffer');
        }
        if(!imagepng($this->resource)){
            throw new \Exception('Failed to write png data');
        }
        if(($output = ob_get_clean()) === false){
            throw new \Exception('Failed to get buffer contents');
        }
        return response($output)->header('Content-Type', 'image/png');
    }

}
