<?php

namespace App\Traits;

trait UploadsImages
{
    public function uploadImage($image, $path)
    {
        // Generate a unique filename for the image
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

        // Move the uploaded image to the specified path
        $image->move($path, $filename);

        return $filename;
    }
}
