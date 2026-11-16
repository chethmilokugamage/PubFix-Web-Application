@extends(view()->exists('layouts.app') ? 'layouts.app' : 'review.layout')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6">

    <div class="mb-4">
        <a href="{{ route('reviews.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to All Reviews
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6 sm:p-8">
        
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-800 font-bold flex items-center justify-center text-lg">
                    {{ strtoupper(substr($review->user->name ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $review->user->name ?? 'Customer' }}</h2>
                    <p class="text-xs text-gray-500">
                        Handyman #{{ $review->handyman_id }} &bull; {{ $review->created_at ? $review->created_at->format('M d, Y') : 'Recent' }}
                    </p>
                </div>
            </div>

            <div>
                @include('components.rating-stars', ['rating' => $review->rating, 'size' => 'w-5 h-5', 'showScore' => true])
            </div>
        </div>

        <div class="py-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Feedback</h3>
            @if($review->comment)
                <p class="text-gray-700 leading-relaxed text-base">{{ $review->comment }}</p>
            @else
                <p class="text-gray-400 italic text-sm">No comment provided.</p>
            @endif
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span>Booking: <strong class="text-gray-700 font-semibold">#{{ $review->booking_id }}</strong></span>
            
            @if($review->isAuthoredBy(auth()->id()))
                <div class="flex items-center gap-3">
                    <a href="{{ route('reviews.edit', $review->id) }}" class="font-medium text-green-700 hover:text-green-900">Edit</a>
                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Delete this review?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                    </form>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
