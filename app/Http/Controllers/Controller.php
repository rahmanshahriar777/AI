<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    public $mimetypes = '';

    public function __construct(){
        $appbrand = view()->shared('appbrand');
        $this->mimetypes = $appbrand ? $appbrand->getAllowedFileType('IMG', true) : '';
    }
}
