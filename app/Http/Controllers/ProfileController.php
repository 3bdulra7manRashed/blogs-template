<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (Name, Email, Short Bio, Biography).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Fill basic fields
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle biography sanitization if provided (for super admin only)
        if (($user->is_super_admin || $user->id === 1) && isset($validated['biography'])) {
            $biographyRaw = $validated['biography'];
            $biographySanitized = null;

            if (!empty($biographyRaw)) {
                // Check if HTMLPurifier is available
                if (class_exists('HTMLPurifier')) {
                    $config = \HTMLPurifier_Config::createDefault();
                    // Allow CKEditor formatting tags including blockquote, table, figure
                    $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ul,ol,li,h1,h2,h3,h4,h5,h6,a[href|target],img[src|alt|width|height],blockquote,table,thead,tbody,tr,th,td,figure,figcaption,div[class],span[class]');
                    $config->set('HTML.TargetBlank', true);
                    $config->set('AutoFormat.AutoParagraph', true);
                    $config->set('AutoFormat.Linkify', true);
                    
                    $purifier = new \HTMLPurifier($config);
                    $biographySanitized = $purifier->purify((string) $biographyRaw);
                } else {
                    // Fallback: Use strip_tags with allowed tags (including blockquote, table elements, figure)
                    $biographySanitized = strip_tags((string) $biographyRaw, '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><blockquote><table><thead><tbody><tr><th><td><figure><figcaption><div><span>');
                    // Clean up attributes on allowed tags (basic sanitization)
                    $biographySanitized = preg_replace('/<a\s+[^>]*href=["\']([^"\']*)["\'][^>]*>/i', '<a href="$1" target="_blank">', $biographySanitized);
                    $biographySanitized = preg_replace('/<img\s+[^>]*src=["\']([^"\']*)["\'][^>]*>/i', '<img src="$1" alt="">', $biographySanitized);
                }
            }

            $user->biography = $biographySanitized;
            Cache::forget('site_owner_bio');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's detailed biography (owner only).
     */
    public function updateBio(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Only allow super admin or user ID 1 to update biography
        if (!$user->is_super_admin && $user->id !== 1) {
            abort(403, 'غير مصرح');
        }

        $data = $request->validate([
            'biography' => 'nullable|string',
        ]);

        $biographyRaw = $data['biography'] ?? null;
        $biographySanitized = null;

        if (!empty($biographyRaw)) {
            // Check if HTMLPurifier is available
            if (class_exists('HTMLPurifier')) {
                $config = \HTMLPurifier_Config::createDefault();
                // Allow CKEditor formatting tags including blockquote, table, figure
                $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ul,ol,li,h1,h2,h3,h4,h5,h6,a[href|target],img[src|alt|width|height],blockquote,table,thead,tbody,tr,th,td,figure,figcaption,div[class],span[class]');
                $config->set('HTML.TargetBlank', true);
                $config->set('AutoFormat.AutoParagraph', true);
                $config->set('AutoFormat.Linkify', true);
                
                $purifier = new \HTMLPurifier($config);
                $biographySanitized = $purifier->purify((string) $biographyRaw);
            } else {
                // Fallback: Use strip_tags with allowed tags (including blockquote, table elements, figure)
                $biographySanitized = strip_tags((string) $biographyRaw, '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><blockquote><table><thead><tbody><tr><th><td><figure><figcaption><div><span>');
                // Clean up attributes on allowed tags (basic sanitization)
                $biographySanitized = preg_replace('/<a\s+[^>]*href=["\']([^"\']*)["\'][^>]*>/i', '<a href="$1" target="_blank">', $biographySanitized);
                $biographySanitized = preg_replace('/<img\s+[^>]*src=["\']([^"\']*)["\'][^>]*>/i', '<img src="$1" alt="">', $biographySanitized);
            }
        }

        $user->biography = $biographySanitized;
        $user->save();

        Cache::forget('site_owner_bio');

        return back()->with('status', 'biography-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
