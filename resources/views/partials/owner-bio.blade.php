@php
  $biography = Cache::remember('site_owner_bio', 3600, function () {
      $owner = \App\Models\User::where('is_super_admin', true)->orWhere('id', 1)->first();
      return $owner ? $owner->biography : null;
  });
@endphp

@if($biography)
  <div class="owner-bio prose prose-lg max-w-none">
    {!! $biography !!}
  </div>
@endif
