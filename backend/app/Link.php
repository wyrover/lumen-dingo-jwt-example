<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\Math;


class Link extends Model
{


    protected $table = 'links';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_url', 'new_url', 'request_count', 'use_count', 'last_used', 'last_requested'
    ];

    protected $dates = ['last_used', 'last_requested'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    

    public function getCode()
    {
        if (!$this->id) {
            throw new Exception;
        }
        return (new Math)->encode($this->id);
    }

    public static function byCode($new_url)
    {
        return static::where('new_url', $new_url)->first();
    }

    public function shortenedUrl()
    {
        return env('CLIENT_URL').'/'.$this->new_url;
    }

    public function touchTimestamp($column)
    {
        $this->{$column} = Carbon::now();
        $this->save();
    }
}