<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\JsonApiCollectionResource;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'event_id' ,'message', 'attachment_path',
    ];

    protected $temp_attachment_file = null;

    public function __construct(array $attributes = [])
    {
        if(isset($attributes['attachment_file']))
            $this->temp_attachment_file = $attributes['attachment_file'];

        parent::__construct($attributes);
    }

    public function getEventComments($args = null) {
        if(!($args == null || isset($args->event_id) || $args->event_id)) {
            throw new \Exception("Please provide a valid event ID");
        }
        $comments = $this->where('event_id',$args->event_id)->get();
        return new JsonApiCollectionResource($comments);
    }

    public function event() {
        return $this->belongsTo('App\Event');
    }

    public function save(array $options = []) {
        if(parent::save($options)) {
            if($this->temp_attachment_file) {
                $destinationPath = storage_path('app/public/media/events/');
                $mimetype = $this->temp_attachment_file->getClientMimeType();
                $guessedExt = $this->temp_attachment_file->guessExtension();
                $this->temp_attachment_file->move($destinationPath, 'image.'.$guessedExt);
            }
        }
    }
}
