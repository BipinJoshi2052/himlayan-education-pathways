<div class="card">
    <div class="card-header"><h5 class="mb-0">Social Share Settings</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="social">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Facebook URL</label>
                        <input type="text" class="form-control" name="social_facebook_url"
                               value="{{ old('social_facebook_url', \App\Models\Setting::get('social_facebook_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" class="form-control" name="social_instagram_url"
                               value="{{ old('social_instagram_url', \App\Models\Setting::get('social_instagram_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">LinkedIn URL</label>
                        <input type="text" class="form-control" name="social_linkedin_url"
                               value="{{ old('social_linkedin_url', \App\Models\Setting::get('social_linkedin_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">YouTube URL</label>
                        <input type="text" class="form-control" name="social_youtube_url"
                               value="{{ old('social_youtube_url', \App\Models\Setting::get('social_youtube_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">TikTok URL</label>
                        <input type="text" class="form-control" name="social_tiktok_url"
                               value="{{ old('social_tiktok_url', \App\Models\Setting::get('social_tiktok_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">X / Twitter Handle</label>
                        <input type="text" class="form-control" name="social_twitter_handle" placeholder="@yourhandle"
                               value="{{ old('social_twitter_handle', \App\Models\Setting::get('social_twitter_handle')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label">Twitter Card Type</label>
                        <select class="form-select" name="twitter_card_type">
                            @foreach (['summary' => 'Summary', 'summary_large_image' => 'Summary (Large Image)'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('twitter_card_type', \App\Models\Setting::get('twitter_card_type', 'summary')) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Social Settings</button>
        </form>
    </div>
</div>
