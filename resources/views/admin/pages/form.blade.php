<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Page Title</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $page->title ?? '') }}" placeholder="e.g. About Us">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
            value="{{ old('slug', $page->slug ?? '') }}" placeholder="auto-generated from title">
        <small class="text-muted">Leave blank to auto-generate from the title.</small>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Content</label>
        <textarea name="content" id="page-content-editor" rows="12"
            class="form-control @error('content') is-invalid @enderror"
            placeholder="Page body">{{ old('content', $page->content ?? '') }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Summernote WYSIWYG editor — free, locally bundled with this admin
             theme (no external CDN or API key needed). It binds to the
             textarea above and keeps its value in sync automatically, so
             the plain-HTML content still posts as before if JS ever fails
             to load. --}}
        <link rel="stylesheet"
            href="{{ asset('assets/admin_assets/node_modules/summernote/dist/summernote-bs4.css') }}">
        <script src="{{ asset('assets/admin_assets/node_modules/summernote/dist/summernote-bs4.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('#page-content-editor').summernote({
                    height: 320,
                    placeholder: 'Write the page content...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'table', 'hr']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            });
        </script>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Meta Title</label>
        <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
            value="{{ old('meta_title', $page->meta_title ?? '') }}">
        @error('meta_title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Meta Description</label>
        <input type="text" name="meta_description"
            class="form-control @error('meta_description') is-invalid @enderror"
            value="{{ old('meta_description', $page->meta_description ?? '') }}">
        @error('meta_description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>
