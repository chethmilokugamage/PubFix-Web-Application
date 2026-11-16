@extends(view()->exists('layouts.app') ? 'layouts.app' : 'review.layout')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6">

    <div class="mb-4">
        <a href="{{ route('reviews.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Reviews
        </a>
    </div>

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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="px-6 py-5 bg-gradient-to-r from-green-700 to-green-800 text-white">
            <h1 class="text-xl font-bold">Write a Review & Rating</h1>
            <p class="text-xs text-green-100 mt-1">Share your feedback about the service provided.</p>
        </div>

        <form action="{{ route('reviews.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="booking_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        Booking ID <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="booking_id" 
                           id="booking_id" 
                           value="{{ old('booking_id', $bookingId ?? '') }}" 
                           placeholder="e.g. 1" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('booking_id') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-2 focus:ring-green-600 text-sm">
                    @error('booking_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="handyman_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        Handyman ID <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="handyman_id" 
                           id="handyman_id" 
                           value="{{ old('handyman_id', $handymanId ?? '') }}" 
                           placeholder="e.g. 1" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('handyman_id') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-2 focus:ring-green-600 text-sm">
                    @error('handyman_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Overall Rating <span class="text-red-500">*</span>
                </label>
                
                <div class="flex items-center gap-2" id="star-rating-container">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" 
                                   name="rating" 
                                   value="{{ $i }}" 
                                   {{ old('rating', 5) == $i ? 'checked' : '' }} 
                                   class="sr-only star-radio">
                            <svg data-value="{{ $i }}" 
                                 class="w-9 h-9 star-icon transition-colors duration-150 {{ old('rating', 5) >= $i ? 'text-amber-400' : 'text-gray-300' }}" 
                                 fill="currentColor" 
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </label>
                    @endfor
                    <span id="rating-label" class="text-sm font-semibold text-gray-700 ml-2">
                        5/5 Stars
                    </span>
                </div>
                @error('rating')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="comment" class="block text-sm font-semibold text-gray-700">
                        Review Comment
                    </label>
                    <span id="char-counter" class="text-xs text-gray-400">0 / 1000</span>
                </div>
                <textarea name="comment" 
                          id="comment" 
                          rows="4" 
                          maxlength="1000"
                          placeholder="Write your feedback here..." 
                          class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('comment') ? 'border-red-500' : 'border-gray-300' }} focus:outline-none focus:ring-2 focus:ring-green-600 text-sm resize-none">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('reviews.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-semibold shadow-sm transition inline-flex items-center gap-2">
                    Submit Review
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('star-rating-container');
    if (!container) return;

    const icons = container.querySelectorAll('.star-icon');
    const radios = container.querySelectorAll('.star-radio');
    const labelSpan = document.getElementById('rating-label');

    function updateStars(val) {
        icons.forEach(icon => {
            const starVal = parseInt(icon.getAttribute('data-value'));
            if (starVal <= val) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-amber-400');
            } else {
                icon.classList.remove('text-amber-400');
                icon.classList.add('text-gray-300');
            }
        });
        if (labelSpan) {
            labelSpan.textContent = val + '/5 Stars';
        }
    }

    radios.forEach(radio => {
        if (radio.checked) {
            updateStars(parseInt(radio.value));
        }

        radio.addEventListener('change', function () {
            updateStars(parseInt(this.value));
        });
    });

    icons.forEach(icon => {
        icon.addEventListener('mouseenter', function () {
            const val = parseInt(this.getAttribute('data-value'));
            updateStars(val);
        });
    });

    container.addEventListener('mouseleave', function () {
        const checked = container.querySelector('.star-radio:checked');
        if (checked) {
            updateStars(parseInt(checked.value));
        }
    });

    const textarea = document.getElementById('comment');
    const counter = document.getElementById('char-counter');
    if (textarea && counter) {
        textarea.addEventListener('input', function () {
            counter.textContent = textarea.value.length + ' / 1000';
        });
        counter.textContent = textarea.value.length + ' / 1000';
    }
});
</script>
@endsection
