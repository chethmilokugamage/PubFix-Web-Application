<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Models\Review;
use App\Services\ReviewService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(Request $request)
    {
        $handymanId = $request->input('handyman_id') ?? $request->input('worker_id');
        $rating = $request->input('rating');

        if ($handymanId) {
            $ratingSummary = $this->reviewService->getHandymanRatingSummary((int) $handymanId);
            $reviews = $this->reviewService->getReviewsForHandyman((int) $handymanId, 10, $rating ? (int) $rating : null);
        } else {
            $ratingSummary = $this->reviewService->getGlobalRatingSummary();
            $reviews = $this->reviewService->getAllReviews(10, $rating ? (int) $rating : null);
        }

        return view('review.index', compact('reviews', 'ratingSummary', 'handymanId', 'rating'));
    }

    public function create(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $handymanId = $request->input('handyman_id');

        $userId = $this->resolveCurrentUserId();
        $booking = null;

        if ($bookingId) {
            $eligibility = $this->reviewService->canUserReviewBooking($userId, (int) $bookingId);

            if (!$eligibility['eligible'] && $eligibility['booking'] !== null) {
                return redirect()->route('reviews.index')->with('error', $eligibility['message']);
            }

            $booking = $eligibility['booking'];
            if (!$handymanId && $booking && isset($booking->handyman_id)) {
                $handymanId = $booking->handyman_id;
            }
        }

        return view('review.create', compact('booking', 'bookingId', 'handymanId'));
    }

    public function store(StoreReviewRequest $request)
    {
        $userId = $this->resolveCurrentUserId();

        try {
            $review = $this->reviewService->createReview($request->validated(), $userId);

            return redirect()
                ->route('reviews.index', ['handyman_id' => $review->handyman_id])
                ->with('success', 'Review submitted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Review $review)
    {
        $review->load(['user', 'booking', 'handyman']);
        return view('review.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $userId = $this->resolveCurrentUserId();

        if (!$review->isAuthoredBy($userId)) {
            return redirect()->route('reviews.index')->with('error', 'Unauthorized access.');
        }

        return view('review.edit', compact('review'));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $userId = $this->resolveCurrentUserId();

        try {
            $this->reviewService->updateReview($review, $request->validated(), $userId);

            return redirect()
                ->route('reviews.index', ['handyman_id' => $review->handyman_id])
                ->with('success', 'Review updated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Review $review)
    {
        $userId = $this->resolveCurrentUserId();

        try {
            $handymanId = $review->handyman_id;
            $this->reviewService->deleteReview($review, $userId);

            return redirect()
                ->route('reviews.index', ['handyman_id' => $handymanId])
                ->with('success', 'Review deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function workerReviews($handymanId, Request $request)
    {
        $rating = $request->input('rating');
        $ratingSummary = $this->reviewService->getHandymanRatingSummary((int) $handymanId);
        $reviews = $this->reviewService->getReviewsForHandyman((int) $handymanId, 10, $rating ? (int) $rating : null);

        return view('review.index', compact('reviews', 'ratingSummary', 'handymanId', 'rating'));
    }

    protected function resolveCurrentUserId()
    {
        return Auth::id() ?? 1;
    }
}
