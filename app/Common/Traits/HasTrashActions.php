<?php

declare(strict_types=1);

namespace App\Common\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

/**
 * Standard restore/force-delete actions for a resource controller whose
 * model uses HandlesTrash. The controller just implements trashModelClass()
 * and adds the two routes from routes/web.php — see docs/trash.md.
 *
 * Authorization: protected by the same `permission:manage-{module}` route
 * middleware as the rest of that controller's resource routes, not a
 * separate Policy — this project doesn't use Policies anywhere else, and
 * introducing them for only these two actions would be a second,
 * inconsistent authorization system living alongside the permission
 * middleware already used for every other admin route.
 */
trait HasTrashActions
{
    abstract protected function trashModelClass(): string;

    public function restore(string $id): RedirectResponse
    {
        $modelClass = $this->trashModelClass();

        /** @var Model $model */
        $model = $modelClass::onlyTrashed()->findOrFail($id);
        $model->restore();

        return back()->with('status', class_basename($model).' restored.');
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $modelClass = $this->trashModelClass();

        /** @var Model $model */
        $model = $modelClass::onlyTrashed()->findOrFail($id);
        $name = class_basename($model);
        $model->forceDelete();

        return back()->with('status_warning', $name.' permanently deleted.');
    }
}
