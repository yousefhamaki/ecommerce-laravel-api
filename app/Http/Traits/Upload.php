<?php
namespace App\Http\Traits;


trait Upload {
    public function Uploadfile($file, $dir, $num) {
        if($file !== NULL){
            $file_extention = $file->getClientOriginalExtension();
            $filename = sha1(time()) . $num . '.' . $file_extention;
            $path = $dir;
            $file->move($path, $filename);
            return $filename;
        }else{
            return NULL;
        }

    }
}
