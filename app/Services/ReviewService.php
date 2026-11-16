<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Worker;
use Exception;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function canUserReviewBooking($userId, $bookingId)
    {
        $booking = $this->findBooking($bookingId);

        if (!$booking) {
            return [
                'eligible' => false,
                'message' => 'Booking not found.',
                'booking' => null,
            ];
        }

        if ((int) $booking->user_id !== (int) $userId) {
            return [
                'eligible' => false,
                'message' => 'You cannot review this booking.',
                'booking' => $booking,
            ];
        }

        $status = strtolower($booking->status ?? 'pending');
        if ($status !== 'completed') {
            return [
                'eligible' => false,
                'message' => 'You can only review completed bookings.',
                'booking' => $booking,
            ];
        }

        $existingReview = Review::where('booking_id', $bookingId)->exists();
        if ($existingReview) {
            return [
                'eligible' => false,
                'message' => 'This booking has already been reviewed.',
                'booking' => $booking,
            ];
        }

        return [
            'eligible' => true,
            'message' => 'Eligible to review.',
            'booking' => $booking,
        ];
    }

    public function createReview(array $data, $userId)
    {
        $bookingId = (int) $data['booking_id'];

        $eligibility = $this->canUserReviewBooking($userId, $bookingId);

        if (!$eligibility['eligible'] && $eligibility['booking'] !== null) {
            throw new Exception($eligibility['message']);
        }

        $alreadyReviewed = Review::where('booking_id', $bookingId)->exists();
        if ($alreadyReviewed) {
            throw new Exception('This booking has already been reviewed.');
        }

        return Review::create([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'handyman_id' => (int) $data['handyman_id'],
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);
    }

    public function updateReview(Review $review, array $data, $userId)
    {
        if (!$review->isAuthoredBy($userId)) {
            throw new Exception('Unauthorized action.');
        }

        $review->update([
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return $review->fresh();
    }

    public function deleteReview(Review $review, $userId)
    {
        if (!$review->isAuthoredBy($userId)) {
            throw new Exception('Unauthorized action.');
        }

        return (bool) $review->delete();
    }

    public function getHandymanRatingSummary($handymanId)
    {
        $query = Review::where('handyman_id', $handymanId);
        $totalReviews = (clone $query)->count();

        if ($totalReviews === 0) {
            return [
                'average_rating' => 0.0,
                'total_reviews' => 0,
                'breakdown' => [
                    5 => ['count' => 0, 'percentage' => 0.0],
                    4 => ['count' => 0, 'percentage' => 0.0],
                    3 => ['count' => 0, 'percentage' => 0.0],
                    2 => ['count' => 0, 'percentage' => 0.0],
                    1 => ['count' => 0, 'percentage' => 0.0],
                ],
                'positive_percentage' => 0.0,
            ];
        }

        $avg = (float) (clone $query)->avg('rating');
        $averageRating = round($avg, 1);

        $counts = (clone $query)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $breakdown = [];
        $positiveCount = 0;

        for ($star = 5; $star >= 1; $star--) {
            $starCount = (int) ($counts[$star] ?? 0);
            $percentage = round(($starCount / $totalReviews) * 100, 1);
            $breakdown[$star] = [
                'count' => $starCount,
                'percentage' => $percentage,
            ];

            if ($star >= 4) {
                $positiveCount += $starCount;
            }
        }

        return [
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'breakdown' => $breakdown,
            'positive_percentage' => round(($positiveCount / $totalReviews) * 100, 1),
        ];
    }

    public function getGlobalRatingSummary()
    {
        $totalReviews = Review::count();

        if ($totalReviews === 0) {
            return [
                'average_rating' => 0.0,
                'total_reviews' => 0,
                'breakdown' => [
                    5 => ['count' => 0, 'percentage' => 0.0],
                    4 => ['count' => 0, 'percentage' => 0.0],
                    3 => ['count' => 0, 'percentage' => 0.0],
                    2 => ['count' => 0, 'percentage' => 0.0],
                    1 => ['count' => 0, 'percentage' => 0.0],
                ],
                'positive_percentage' => 0.0,
            ];
        }

        $avg = (float) Review::avg('rating');
        $averageRating = round($avg, 1);

        $counts = Review::select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $breakdown = [];
        $positiveCount = 0;

        for ($star = 5; $star >= 1; $star--) {
            $starCount = (int) ($counts[$star] ?? 0);
            $percentage = round(($starCount / $totalReviews) * 100, 1);
            $breakdown[$star] = [
                'count' => $starCount,
                'percentage' => $percentage,
            ];

            if ($star >= 4) {
                $positiveCount += $starCount;
            }
        }

        return [
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'breakdown' => $breakdown,
            'positive_percentage' => round(($positiveCount / $totalReviews) * 100, 1),
        ];
    }

    public function getReviewsForHandyman($handymanId, $perPage = 10, $ratingFilter = null)
    {
        $query = Review::with(['user', 'booking'])->forHandyman($handymanId);

        if ($ratingFilter) {
            $query->withRating($ratingFilter);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getReviewsByUser($userId, $perPage = 10)
    {
        return Review::with(['handyman', 'booking'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getAllReviews($perPage = 10, $ratingFilter = null, $handymanId = null)
    {
        $query = Review::with(['user', 'booking']);

        if ($handymanId) {
            $query->forHandyman($handymanId);
        }

        if ($ratingFilter) {
            $query->withRating($ratingFilter);
        }

        return $query->latest()->paginate($perPage);
    }

    protected function findBooking($bookingId)
    {
        try {
            if (class_exists(Booking::class)) {
                $booking = Booking::find($bookingId);
                if ($booking) {
                    return $booking;
                }
            }
        } catch (\Throwable $e) {
        }

        try {
            return DB::table('bookings')->where('id', $bookingId)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
