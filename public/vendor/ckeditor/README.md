# CKEditor Assets

This directory should contain the CKEditor 5 Classic Build files.

## Installation Steps

1. Download CKEditor 5 Classic Build from:
   - https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js
   - https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ar.js

2. Place the files in this directory:
   - `resources/ckeditor/ckeditor.js`
   - `resources/ckeditor/translations/ar.js`

3. Run the publish command:
   ```bash
   php artisan vendor:publish --tag=ckeditor-assets
   ```

4. Ensure storage link is created:
   ```bash
   php artisan storage:link
   ```

## Alternative: Use CDN

If you prefer using CDN instead of local files, you can modify the `resources/views/components/ckeditor-scripts.blade.php` file to use CDN URLs instead of `asset()` URLs.

## Files Structure

After downloading and publishing, your structure should be:
```
resources/ckeditor/
├── README.md
├── ckeditor.js
└── translations/
    └── ar.js

public/vendor/ckeditor/
├── ckeditor.js
└── translations/
    └── ar.js
```

