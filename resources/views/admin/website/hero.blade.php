@php
    $layouts = [
        'classic' => [
            'label' => 'Classic (side by side)',
            'description' => 'Title, description and buttons on the left; the slide image on the right. The current design.',
        ],
        'full_image' => [
            'label' => 'Full-width image overlay',
            'description' => 'The slide image fills the whole banner edge to edge; the title, description and buttons sit on top of it.',
        ],
    ];
@endphp

<div class="card">
    <div class="card-body">
        <p class="text-muted mb-4">
            Choose how the home page hero banner is laid out. The slides themselves — title, description, image
            and buttons — are still edited under
            <a href="{{ route('admin.sections.index') }}">Content Sections &rarr; Hero banner (slides)</a>.
            One slide shows as a single banner; two or more automatically become a carousel, in either design.
        </p>

        <form method="POST" action="{{ route('admin.website.update') }}">
            @csrf
            <input type="hidden" name="group" value="hero">

            <div class="row g-3">
                @foreach ($layouts as $value => $layout)
                    <div class="col-md-6">
                        <label class="hero-layout-option d-block h-100 p-3 border rounded {{ $heroLayout === $value ? 'border-primary bg-light' : '' }}" style="cursor: pointer;">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <input type="radio" name="hero_layout" value="{{ $value }}" class="form-check-input mt-1" @checked($heroLayout === $value)>
                                <span class="fw-semibold">{{ $layout['label'] }}</span>
                            </div>

                            @if ($value === 'classic')
                                <div class="border rounded mb-2 d-flex align-items-center justify-content-between p-2" style="background: #eef1fb;">
                                    <div style="width: 55%;">
                                        <div style="height: 8px; width: 70%; background: #525fe1; border-radius: 2px; margin-bottom: 6px;"></div>
                                        <div style="height: 5px; width: 90%; background: #c9cdf0; border-radius: 2px; margin-bottom: 4px;"></div>
                                        <div style="height: 5px; width: 60%; background: #c9cdf0; border-radius: 2px;"></div>
                                    </div>
                                    <div style="width: 35%; height: 50px; background: #c9cdf0; border-radius: 4px;"></div>
                                </div>
                            @else
                                <div class="border rounded mb-2 position-relative d-flex align-items-end p-2" style="background: #525fe1; height: 90px;">
                                    <div>
                                        <div style="height: 8px; width: 90px; background: #fff; border-radius: 2px; margin-bottom: 6px;"></div>
                                        <div style="height: 5px; width: 60px; background: rgba(255,255,255,.7); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            @endif

                            <p class="text-muted small mb-0">{{ $layout['description'] }}</p>
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-4">Save</button>
        </form>
    </div>
</div>
