# CKEditor 5 Integration — Changes & Notes

Purpose: document all edits and setup steps made for adding CKEditor 5 with image upload support to this project.

---

## Summary of touched files
- `resources/views/welcome.blade.php`
  - Added CKEditor CDN, editor textarea, editor initialization script, editor height CSS and CSRF usage.
- `app/Http/Controllers/ImageUploadController.php`
  - Implemented image validation, saving to `public/images`, and JSON response containing uploaded image URL.
- `routes/web.php`
  - Added POST route `/upload-image` → `ImageUploadController@store`.
- `public/images/`
  - Created directory to store uploaded images.

---

## Detailed changes

### 1) Blade view (`resources/views/welcome.blade.php`)
- Ensure CSRF meta tag exists:
  - `<meta name="csrf-token" content="{{ csrf_token() }}">`
- Add CKEditor CDN (example version; change as needed):
  - `<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>`
- Add editor textarea:
  - `<textarea id="editor" name="editor"></textarea>`
- Add CSS to enlarge editor area:
  - `.ck-editor__editable { min-height: 600px !important; }`
- Initialize ClassicEditor with simpleUpload pointed to the backend route and CSRF header:
  - simpleUpload expects `uploadUrl` and `headers` with `X-CSRF-TOKEN`.
- Toolbar must include `bulletedList` and `numberedList` so lists show.

Minimal example initialization (paste into Blade):
```html
<script>
ClassicEditor
  .create(document.querySelector('#editor'), {
    toolbar: [
      'heading','|','bold','italic','underline','strikethrough','|',
      'bulletedList','numberedList','|',
      'link','blockQuote','insertTable','imageUpload','|','undo','redo'
    ],
    image: {
      toolbar: [ 'imageTextAlternative', 'imageStyle:full', 'imageStyle:side' ]
    },
    simpleUpload: {
      uploadUrl: '{{ route("upload.image") }}',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    }
  })
  .catch( error => console.error(error) );
</script>
```

---

### 2) Route (`routes/web.php`)
Add this route if not present:
```php
// filepath: c:\Users\Lenovo\LaravelPrj\ckedit\routes\web.php
// ...existing code...
Route::post('/upload-image', [App\Http\Controllers\ImageUploadController::class, 'store'])->name('upload.image');
// ...existing code...
```

---

### 3) Controller (`app/Http/Controllers/ImageUploadController.php`)
Example robust `store` method (validates, saves and returns URL JSON):

```php
// filepath: c:\Users\Lenovo\LaravelPrj\ckedit\app\Http\Controllers\ImageUploadController.php
// ...existing code...
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120'
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug(substr($filename, 0, 50));
            $filename = $safe . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('images'), $filename);

            $url = asset('images/' . $filename);

            return response()->json([
                'url' => $url,
                'message' => 'Image uploaded successfully'
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
// ...existing code...
```

Notes:
- CKEditor `simpleUpload` will POST the file field named `upload` by default. (If using a custom adapter, ensure the field name matches.)
- Return JSON with `url` containing the publicly accessible image URL.

---

### 4) Create images directory
Create `public/images` folder (Windows command):
```
mkdir c:\Users\Lenovo\LaravelPrj\ckedit\public\images
```

Ensure webserver can serve that folder (no special permissions usually needed on Windows).

---

## Quick manual test
1. Start Laravel:
```
php artisan serve
```
2. Open the page with the editor (http://127.0.0.1:8000).
3. Use the image upload toolbar button, select an image.
4. If upload fails, inspect browser devtools Network tab for POST `/upload-image` and the response.

You can also test with curl:
```
curl -F "upload=@C:/path/to/photo.jpg" http://127.0.0.1:8000/upload-image
```

---

## Troubleshooting
- If lists not visible: verify toolbar includes `bulletedList` and `numberedList`.
- If image not inserted: check response JSON contains `url` and status 200; check console errors in browser.
- If upload blocked by CSRF: confirm Blade has the CSRF meta tag and that the upload request includes `X-CSRF-TOKEN`.

---

End of document.