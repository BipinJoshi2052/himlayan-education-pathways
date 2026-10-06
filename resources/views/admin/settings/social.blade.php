<div class="card">
    <div class="card-header"><h5 class="mb-0">Social Share Settings</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="social">

            {{-- Facebook --}}
            <div class="card mb-4 border">
                <div class="card-header"><h6 class="mb-0">Facebook</h6></div>
                <div class="card-body">
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
                                <label class="form-label">Facebook Page ID</label>
                                <input type="text" class="form-control" name="facebook_page_id"
                                       value="{{ old('facebook_page_id', \App\Models\Setting::get('facebook_page_id')) }}"
                                       placeholder="e.g. 1016268754913107">
                                <small class="text-muted">Shows the Page's latest posts and counts on the About page. Leave empty to hide the feed.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label">Facebook Page Access Token</label>
                                <input type="password" class="form-control" name="facebook_page_token" autocomplete="new-password"
                                       placeholder="{{ \App\Models\Setting::get('facebook_page_token') ? 'Saved — leave blank to keep it' : 'Paste the Page access token' }}">
                                <small class="text-muted">Stored encrypted. Leave blank to keep the saved token.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TikTok --}}
            <div class="card mb-4 border">
                <div class="card-header"><h6 class="mb-0">TikTok</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">TikTok URL</label>
                                <input type="text" class="form-control" name="social_tiktok_url"
                                       value="{{ old('social_tiktok_url', \App\Models\Setting::get('social_tiktok_url')) }}"
                                       placeholder="https://www.tiktok.com/@yourhandle">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="form-label">TikTok video links</label>
                                <textarea class="form-control" name="tiktok_video_links" rows="5"
                                          placeholder="https://www.tiktok.com/@yourhandle/video/1234567890&#10;https://www.tiktok.com/@yourhandle/video/0987654321">{{ old('tiktok_video_links', \App\Models\Setting::get('tiktok_video_links')) }}</textarea>
                                <small class="text-muted">One video link per line. Up to 12 are shown on the About page, in the order listed. Each link is checked with TikTok when saved; anything that isn't a TikTok video link is ignored.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Other platforms --}}
            <div class="card mb-4 border">
                <div class="card-header"><h6 class="mb-0">Instagram, LinkedIn, YouTube</h6></div>
                <div class="card-body">
                    <div class="row">
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
                            <div class="form-group mb-0">
                                <label class="form-label">YouTube URL</label>
                                <input type="text" class="form-control" name="social_youtube_url"
                                       value="{{ old('social_youtube_url', \App\Models\Setting::get('social_youtube_url')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- X / Twitter --}}
            <div class="card mb-4 border">
                <div class="card-header"><h6 class="mb-0">X / Twitter</h6></div>
                <div class="card-body">
                    <div class="row">
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
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Social Settings</button>
        </form>
    </div>
</div>
