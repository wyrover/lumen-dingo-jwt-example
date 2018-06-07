<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\Http\Controllers\Api\V1\Controller;
use App\Link;
use Cache;


class LinkController extends Controller
{
       

    public function store(Request $request) 
    {
        $this->validate($request, [
            'url' => 'required|url'
        ], [
            'url.required' => 'Please enter a URL',
            'url.url' => 'Not a valid URL'
        ]);
        $link = Link::firstOrNew([
            'original_url' => $request->url
        ]);
        if (!$link->exists) {
            $link->new_url = app('hash')->make($request->url);
            $link->save();
        }
        $link->increment('request_count');
        $link->touchTimestamp('last_requested');
        return response()->json([
            'original_url' => $link->original_url,
            'new_url' => $link->new_url,
        ], 201);       
    }

    public function show($code_id)
    {
        $link = Cache::rememberForever("link.{$code_id}", function () use ($code_id)
        {
            return Link::byCode($code_id);
        });

        if (!$link) {
            return response(null, 404);
        }

        $link->increment('use_count');
        $link->touchTimestamp('last_used');

        return response()->json([
            'original_url' => $link->original_url,
            'shortened_url' => $link->shortenedUrl(),
        ], 200);
    }

    public function show2($code_id)
    {
        $link = Cache::remember("stats.{$code_id}", 10, function () use ($code_id)
        {
            return Link::byCode($code_id);
        });

        return response()->json([
            'original_url' => $link->original_url,
            'new_url' => $link->new_url,
            'shortened_url' => $link->shortenedUrl(),
            'use_count' => (int) $link->use_count,
            'request_count' => (int) $link->request_count,
            'last_requested' => $link->last_requested->toDateTimeString(),
            'last_used' => $link->last_used ? $link->last_used->toDateTimeString() : null,
        ], 200);
    }

   

    
}