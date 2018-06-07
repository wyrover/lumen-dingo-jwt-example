<?php
namespace App\Http\Controllers\Api\V1;

use App\Tag;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\TagTransformer;

class TagController extends Controller
{
    
    public function index()
    {
        return Tag::all()->toJson();
    }
    
    public function store(Request $request) 
    {
       /*  $args = $request->all();
        $post = new Post();
        $post->title = $args['input']['title'];
        $post->text  = $args['input']['text'];
        $post->save();
        return $post; */
    }
    
    public function update($id, Request $request) 
    {
        //
    }
}