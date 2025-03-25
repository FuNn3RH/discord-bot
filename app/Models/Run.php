<?php
namespace App\Models;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Run extends Model {
    use SoftDeletes;

    protected $fillable = [
        'count', 'level', 'paid_at',
        'price', 'unit', 'adv',
        'note', 'paid', 'dmessage_link',
        'depleted', 'user_id', 'message',
        'channel_id', 'dmessage_id',
        'dungeons', 'boosters', 'boosters_count', 'pot',
    ];

    protected $casts = [
        'boosters' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }
}
