<details class="theme-picker" data-theme-picker>
    <summary class="btn-header">
        <span class="theme-dot" aria-hidden="true"></span>
        <span class="theme-picker-label">{{ __('Mode') }}</span>
    </summary>
    <form method="POST" action="{{ route('theme.update') }}" class="theme-menu">
        @csrf
        @method('PUT')
        @foreach ($themes as $option)
            <button type="submit" name="theme" value="{{ $option->value }}" data-dark="{{ $option->isDark() ? 'true' : 'false' }}" aria-pressed="{{ $option === $theme ? 'true' : 'false' }}" class="theme-option">
                <span class="theme-preview" data-theme="{{ $option->value }}" aria-hidden="true"></span>
                <span>{{ __($option->label()) }}</span>
            </button>
        @endforeach
    </form>
</details>
