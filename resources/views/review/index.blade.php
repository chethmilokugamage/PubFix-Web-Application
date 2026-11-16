@extends(view()->exists('layouts.app') ? 'layouts.app' : 'review.layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded-r-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 text-sm font-semibold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-r-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm font-semibold">&times;</button>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                @if(isset($handymanId) && $handymanId)
                    Handyman #{{ $handymanId }} - Reviews & Ratings
                @else
                    Ratings & Reviews
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">Customer ratings and feedback.</p>
        </div>

        <div class="flex items-center gap-3">
            @if(isset($handymanId) && $handymanId)
                <a href="{{ route('reviews.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">
                    &larr; View All Reviews
                </a>
            @endif
            <a href="{{ route('reviews.create', array_filter(['handyman_id' => $handymanId ?? null])) }}" class="inline-flex items-center gap-2 bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-green-800 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Write a Review
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            
            <div class="md:col-span-4 text-center md:text-left md:border-r md:border-gray-100 md:pr-8">
                <div class="text-5xl font-black text-gray-900 tracking-tight">
                    {{ number_format($ratingSummary['average_rating'], 1) }}
                </div>
                <div class="mt-2 flex justify-center md:justify-start items-center">
                    @include('components.rating-stars', ['rating' => $ratingSummary['average_rating'], 'size' => 'w-6 h-6'])
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    Based on <span class="font-semibold text-gray-800">{{ $ratingSummary['total_reviews'] }}</span> 
                    {{ \Illuminate\Support\Str::plural('review', $ratingSummary['total_reviews']) }}
                </p>
                @if($ratingSummary['total_reviews'] > 0)
                    <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $ratingSummary['positive_percentage'] }}% positive satisfaction
                    </div>
                @endif
            </div>

            <div class="md:col-span-8 space-y-2.5">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php
                        $data = $ratingSummary['breakdown'][$star] ?? ['count' => 0, 'percentage' => 0];
                    @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('reviews.index', array_filter(['handyman_id' => $handymanId ?? null, 'rating' => $star])) }}" 
                           class="w-16 font-medium text-gray-600 hover:text-green-700 flex items-center gap-1">
                            <span>{{ $star }}</span>
                            <span class="text-amber-400">★</span>
                        </a>

                        <div class="flex-grow bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-amber-400 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $data['percentage'] }}%;"></div>
                        </div>

                        <div class="w-16 text-right text-xs text-gray-500">
                            {{ $data['count'] }} ({{ $data['percentage'] }}%)
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 bg-white p-3 rounded-lg border border-gray-200">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-1">Filter Rating:</span>
            
            <a href="{{ route('reviews.index', array_filter(['handyman_id' => $handymanId ?? null])) }}" 
               class="px-3 py-1 text-xs font-medium rounded-full transition {{ !request()->filled('rating') ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All ({{ $ratingSummary['total_reviews'] }})
            </a>

            @foreach([5, 4, 3, 2, 1] as $star)
                @php
                    $starCount = $ratingSummary['breakdown'][$star]['count'] ?? 0;
                @endphp
                <a href="{{ route('reviews.index', array_filter(['handyman_id' => $handymanId ?? null, 'rating' => $star])) }}" 
                   class="px-3 py-1 text-xs font-medium rounded-full transition {{ request('rating') == $star ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $star }} Stars ({{ $starCount }})
                </a>
            @endforeach
        </div>

        @if(request()->filled('rating') || request()->filled('handyman_id'))
            <a href="{{ route('reviews.index') }}" class="text-xs font-medium text-red-600 hover:text-red-800 transition">
                Reset Filters &times;
            </a>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-gray-300 transition">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-3">
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-800 font-bold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($review->user->name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">
                                {{ $review->user->name ?? 'Customer' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if(isset($review->booking_id))
                                    Booking #{{ $review->booking_id }} &bull;
                                @endif
                                <span>{{ $review->created_at ? $review->created_at->diffForHumans() : 'Recently' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @include('components.rating-stars', ['rating' => $review->rating, 'size' => 'w-4 h-4', 'showScore' => true])

                        @if($review->isAuthoredBy(auth()->id()))
                            <div class="flex items-center gap-2">
                                <a href="{{ route('reviews.edit', $review->id) }}" class="text-xs text-gray-500 hover:text-gray-800 font-medium">Edit</a>
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                @if($review->comment)
                    <p class="text-gray-700 text-sm leading-relaxed mt-3 pl-0 sm:pl-13">
                        {{ $review->comment }}
                    </p>
                @else
                    <p class="text-gray-400 italic text-xs mt-2 pl-0 sm:pl-13">
                        No written comment provided.
                    </p>
                @endif

                @if(!isset($handymanId) && isset($review->handyman_id))
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Handyman: <a href="{{ route('reviews.worker', $review->handyman_id) }}" class="text-green-700 font-medium hover:underline">Worker #{{ $review->handyman_id }}</a></span>
                        <a href="{{ route('reviews.show', $review->id) }}" class="text-gray-400 hover:text-gray-600">View</a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 text-center py-12 px-4">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">No reviews found</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto mt-1">
                    @if(request()->filled('rating'))
                        There are no {{ request('rating') }}-star reviews yet.
                    @else
                        No feedback has been submitted yet.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('reviews.create', array_filter(['handyman_id' => $handymanId ?? null])) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Leave a Review
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif

</div>
@endsection
