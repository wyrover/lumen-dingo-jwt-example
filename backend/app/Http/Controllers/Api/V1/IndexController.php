<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct()
    {
    }

    public function hello()
    {
        return response()->json([
            'message' => 'Hello World! V1',
        ]);
        //return 'Hello World! V1';
    }
    
}