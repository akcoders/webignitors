@if (session('status') || session('success') || $errors->has('report'))
    <div class="global-flash">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-info border-0 mb-0" role="status">{{ session('status') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success border-0 mb-0" role="status">{{ session('success') }}</div>
            @endif
            @error('report')
                <div class="alert alert-danger border-0 mb-0" role="alert">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif
