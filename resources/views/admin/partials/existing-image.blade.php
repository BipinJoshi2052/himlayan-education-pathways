{{--
    An uploaded image shown on an edit form with a × button. Clicking the
    button hides the image and sets the hidden flag to 1, so the save removes
    the image. Choosing a new file in the input beside it replaces the image
    instead (the controller ignores the flag when a file is sent).

    Params:
      $url         the image address
      $alt         alt text
      $removeName  the flag field name, e.g. "remove_image" or "items[3][remove_image]"
      $style       optional inline style for the image
--}}
<div class="existing-image position-relative mb-2" style="display: inline-block;">
    <img src="{{ $url }}" alt="{{ $alt }}" class="rounded" style="{{ $style ?? 'max-height: 160px;' }} display:block;">
    <button type="button" class="btn btn-sm btn-danger existing-image-remove position-absolute top-0 end-0 m-1"
            aria-label="Remove image" title="Remove image" style="line-height: 1; padding: 2px 8px;">&times;</button>
    <input type="hidden" name="{{ $removeName }}" value="0" class="existing-image-flag">
</div>
