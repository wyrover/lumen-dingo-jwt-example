<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Post;
use App\Transformers\PostTransformer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    
    public function index()
    {
        //return $this->collection(Post::paginate(10), new PostTransformer());
        return Post::paginate(10);
    }

    public function show($id) 
    {
        return $this->item(Post::findOrFail($id), new PostTransformer());        
    }

    public function store(Request $request) 
    {
        $args = $request->all();
        $post = new Post();
        $post->subject = $args['input']['subject'];
        $post->body  = $args['input']['body'];
        $post->save();
        $post->tags()->sync($args['input']['checked']);
        return $post;
    }

    public function update($id, Request $request) 
    {
        $rules = array(
            'subject'   => 'required|string',
            'body'      => 'required|string',
        );
        $this->validate(Request::instance(), $rules);

        $post = Post::findOrFail($id);         
        $post->subject = Input::get('subject');        
        $post->body  = Input::get('body');
        $post->save();
        return $post;
    }


    public function byTag($id)
    {
        $posts = Post::whereHas('tags', function($query) use ($id) {
            $query->where('id', $id);
        });
        return $this->collection(
            $posts->paginate(10), new PostTransformer()
        );
    }

    /**
     * Update post<br>
     * Only if the Posts' policy allows it
     *
     * @return \Illuminate\Http\JsonResponse
     */
//    public function update($post_id)
//    {
//        $rules = array(
//            'subject'   => 'required|string',
//            'body'      => 'required|string',
//        );
//        $this->validate(Request::instance(), $rules);
//        $post = Post::find($post_id);
//        $this->authorize('update', $post);
//        try {
//            $post->subject = Input::get('subject');
//            $post->body = Input::get('body');
//            $post->save();
//            return response()->json($post);
//        } catch (\Exception $e) {
//            return response()->json([
//                'message' => 'Post not updated',
//                'error' => $e->getMessage()
//            ], 400);
//        }
//    }
}