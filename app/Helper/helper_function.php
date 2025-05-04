<?php

use Illuminate\Support\Facades\Storage;

function privateAsset($path){
   return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(30));
}
