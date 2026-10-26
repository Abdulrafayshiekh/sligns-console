<div class="notes-card">
    <h3>{{ __('Private notes') }}</h3>
    <div class="notes-scroll mb-2">
        @forelse ($notes as $note)
            <div class="note-item">
                <time datetime="{{ $note->noted_on->toDateString() }}">{{ $note->noted_on->translatedFormat('j M Y') }}</time>
                {{ $note->body }}
            </div>
        @empty
            <p class="text-muted small mb-0">{{ __('No notes yet.') }}</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('coach.notes.store', $client) }}" novalidate>
        @csrf
        <textarea name="body" rows="2" class="form-control form-control-sm mb-2 @error('body') is-invalid @enderror" placeholder="{{ __('Write a dated note…') }}" aria-label="{{ __('Note') }}">{{ old('body') }}</textarea>
        @error('body')<div class="invalid-feedback mb-2">{{ $message }}</div>@enderror
        <div class="d-flex gap-2">
            <input type="date" name="noted_on" value="{{ old('noted_on', today()->toDateString()) }}" max="{{ today()->toDateString() }}" class="form-control form-control-sm" aria-label="{{ __('Note date') }}">
            <button type="submit" class="btn btn-sm btn-brand text-nowrap">+ {{ __('Add note') }}</button>
        </div>
    </form>
    <p class="text-muted small mt-2 mb-0">{{ __('Private to you. Notes never transfer to another coach.') }}</p>
</div>
