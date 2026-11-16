<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'handyman_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function handyman()
    {
        if (class_exists(Worker::class)) {
            return $this->belongsTo(Worker::class, 'handyman_id');
        }

        return $this->belongsTo(User::class, 'handyman_id');
    }

    public function worker()
    {
        return $this->handyman();
    }

    public function isAuthoredBy($userId)
    {
        return $userId && (int) $this->user_id === (int) $userId;
    }

    public function scopeForHandyman($query, $handymanId)
    {
        return $query->where('handyman_id', $handymanId);
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
}
