@if (session('status'))
    <div class="alert alert-success py-2">{{ session('status') }}</div>
@endif

@error('coach')
    <div class="alert alert-warning py-2">{{ $message }}</div>
@enderror
