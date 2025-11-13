<?php

namespace App\Policies;

use App\Enums\ExportStatus;
use App\Models\Export;
use App\Models\User;

class ExportPolicy
{
    /**
     * Determine if the user can create exports
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the export
     *
     * @param User $user
     * @param Export $export
     * @return bool
     */
    public function view(User $user, Export $export): bool
    {
        return $user->isManager() || $user->id === $export->user_id;
    }

    /**
     * Determine if the user can download the export
     *
     * @param User $user
     * @param Export $export
     * @return bool
     */
    public function download(User $user, Export $export): bool
    {
        if ($export->status !== ExportStatus::READY) {
            return false;
        }

        return $user->isManager() || $user->id === $export->user_id;
    }
}
