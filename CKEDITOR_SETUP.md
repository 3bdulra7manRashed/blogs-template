# CKEditor 5 - Centralized Implementation Guide

This Laravel project now uses a centralized, reusable CKEditor 5 implementation. CKEditor is managed through a Service Provider with custom Blade directives, making it easy to add rich text editing to any form across your application.

## Quick Start

### 1. Download CKEditor Assets

Download the following files and place them in `resources/ckeditor/`:

- **CKEditor Main Script**: https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js
- **Arabic Translation**: https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ar.js

```bash
# Using PowerShell (Windows)
cd "resources/ckeditor"
Invoke-WebRequest -Uri "https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js" -OutFile "ckeditor.js"
Invoke-WebRequest -Uri "https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ar.js" -OutFile "translations/ar.js"

# Or using curl (Linux/Mac)
cd resources/ckeditor
curl -O https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js
cd translations
curl -O https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ar.js
```

### 2. Publish CKEditor Assets

Run the following command to copy CKEditor files from `resources/ckeditor` to `public/vendor/ckeditor`:

```bash
php artisan vendor:publish --tag=ckeditor-assets
```

### 3. Create Storage Symlink

Ensure the storage symlink exists for image uploads:

```bash
php artisan storage:link
```

### 4. Clear Application Cache

```bash
php artisan optimize:clear
```

## Usage in Blade Views

### Basic Usage

To add CKEditor to any Blade view, simply use the `@ckeditor` directive:

```blade
<form action="/posts" method="POST">
    @csrf
    
    <label>Content</label>
    @ckeditor('content')
    
    <button type="submit">Save</button>
</form>
```

### With Existing Content (Edit Forms)

When editing existing content, set the `$value` variable before calling the directive:

```blade
<form action="/posts/{{ $post->id }}" method="POST">
    @csrf
    @method('PUT')
    
    <label>Content</label>
    @php $value = old('content', $post->content); @endphp
    @ckeditor('content')
    
    <button type="submit">Update</button>
</form>
```

### Include Scripts

Add the `@ckeditorScripts` directive in your layout file (preferably before `</body>`):

```blade
@push('scripts')
@ckeditorScripts
@endpush
```

Or in your layout directly:

```blade
<body>
    <!-- Your content -->
    
    @stack('scripts')
    @ckeditorScripts
</body>
```

## Image Upload

CKEditor is configured to automatically upload images to `/ckeditor/upload` when users paste or insert images. Images are stored in `storage/app/public/ckeditor/`.

**Upload Route**: `POST /ckeditor/upload`  
**Controller**: `App\Http\Controllers\CkeditorController@upload`  
**Storage Path**: `storage/app/public/ckeditor/`  
**Max Upload Size**: 5MB  
**Allowed Types**: jpeg, png, jpg, gif, webp

### CSRF Protection

CSRF tokens are automatically handled. Ensure your layout includes:

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## Configuration

### Customizing Editor Options

To customize CKEditor configuration (toolbar, language, plugins, etc.), edit:

```
resources/views/components/ckeditor-scripts.blade.php
```

### Customizing Styles

CKEditor styles can be customized in the same file. Current configuration includes:

- Min height: 700px
- RTL support for Arabic
- Professional typography
- Custom scrollbars

### Upload Validation

To change upload limits or allowed file types, edit:

```
app/Http/Controllers/CkeditorController.php
```

## Using CKEditor in Other Views

You can now use CKEditor anywhere in your application:

### Example: Comments Form

```blade
<form action="/comments" method="POST">
    @csrf
    @ckeditor('comment_body')
    <button type="submit">Post Comment</button>
</form>
```

### Example: Profile Bio

```blade
<form action="/profile" method="POST">
    @csrf
    @php $value = old('bio', auth()->user()->bio); @endphp
    @ckeditor('bio')
    <button type="submit">Update Bio</button>
</form>
```

## Updating CKEditor

To update to a newer version of CKEditor:

1. Download the new version files to `resources/ckeditor/`
2. Re-publish assets:
   ```bash
   php artisan vendor:publish --tag=ckeditor-assets --force
   ```
3. Clear cache:
   ```bash
   php artisan optimize:clear
   ```

## Alternative: Using CDN

If you prefer using CDN instead of local files, modify `resources/views/components/ckeditor-scripts.blade.php`:

Change:
```blade
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
```

To:
```blade
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
```

## Troubleshooting

### Editor Not Appearing

1. Ensure `@ckeditorScripts` is included in your layout
2. Check browser console for JavaScript errors
3. Verify assets are published: Check `public/vendor/ckeditor/ckeditor.js` exists
4. Clear application cache: `php artisan optimize:clear`

### Images Not Uploading

1. Ensure storage link exists: `php artisan storage:link`
2. Check `storage/app/public/ckeditor/` directory is writable
3. Verify CSRF token meta tag exists in your layout
4. Check file size (max 5MB) and type (jpeg, png, jpg, gif, webp)

### Multiple Editors on Same Page

The implementation automatically handles multiple editors on the same page. Each `.ckeditor` textarea will be initialized independently.

## Testing

### Test Publishable Assets

```bash
php artisan test --filter=CkeditorAssetsTest
```

### Test Upload Route

```php
public function test_ckeditor_upload_route_returns_url()
{
    Storage::fake('public');
    
    $file = UploadedFile::fake()->image('test.jpg', 1000, 1000);
    
    $response = $this->actingAs($user)
        ->post(route('ckeditor.upload'), [
            'upload' => $file
        ]);
    
    $response->assertStatus(200)
        ->assertJsonStructure(['url', 'uploaded', 'fileName']);
        
    Storage::disk('public')->assertExists('ckeditor/' . $file->hashName());
}
```

## Architecture

### Files Structure

```
app/
├── Http/
│   └── Controllers/
│       └── CkeditorController.php          # Handles image uploads
└── Providers/
    └── CKEditorServiceProvider.php         # Registers directives and assets

resources/
├── ckeditor/                                # Source CKEditor files
│   ├── ckeditor.js
│   ├── translations/
│   │   └── ar.js
│   └── README.md
└── views/
    └── components/
        ├── ckeditor-scripts.blade.php      # Scripts and initialization
        └── ckeditor-field.blade.php        # Textarea field

public/
└── vendor/
    └── ckeditor/                            # Published assets (after vendor:publish)
        ├── ckeditor.js
        └── translations/
            └── ar.js

storage/
└── app/
    └── public/
        └── ckeditor/                        # Uploaded images

routes/
└── web.php                                  # Contains /ckeditor/upload route
```

### Blade Directives

- **`@ckeditorScripts`**: Outputs CKEditor scripts and initialization (use once per page)
- **`@ckeditor('fieldName')`**: Creates a textarea that will be initialized as CKEditor

### Service Provider

`App\Providers\CKEditorServiceProvider` is registered in `config/app.php` and handles:

1. Publishing assets from `resources/ckeditor` to `public/vendor/ckeditor`
2. Registering Blade directives
3. Auto-initialization of all `.ckeditor` textareas

---

**Need Help?** Check the official CKEditor 5 documentation: https://ckeditor.com/docs/ckeditor5/

