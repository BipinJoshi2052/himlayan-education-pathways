{{--
    Usage: @include('admin.partials.row-actions', ['routeBase' => 'admin.sections', 'model' => $section])
    Shared across every trashable module's list view — edit/delete when
    viewing active records, restore/force-delete when viewing trash.
    Pass 'showEdit' => false for a module with no edit route (Inquiries —
    its "edit" equivalent is the show page, which the row already links to).
--}}
@php
    $showEdit ??= true;
@endphp
@if ($model->trashed())
    <form method="POST" action="{{ route($routeBase.'.restore', $model->id) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-soft-success">Restore</button>
    </form>
    <form method="POST" action="{{ route($routeBase.'.forceDelete', $model->id) }}" class="d-inline"
          data-confirm="Permanently delete this?" data-confirm-text="This cannot be undone — the record and any attached files will be gone for good."
          data-confirm-button="Yes, delete forever">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Delete Permanently</button>
    </form>
@else
    @if ($showEdit)
        <a href="{{ route($routeBase.'.edit', $model) }}" class="btn btn-sm btn-soft-primary">Edit</a>
    @endif
    <form method="POST" action="{{ route($routeBase.'.destroy', $model) }}" class="d-inline"
          data-confirm="Delete this?">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
    </form>
@endif
