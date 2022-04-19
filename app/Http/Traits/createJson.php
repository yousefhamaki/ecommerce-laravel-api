<?php
namespace App\Http\Traits;

trait createJson {
    public function makeJson($text) {
        return json_encode($text);
    }
}
