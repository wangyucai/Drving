<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'type' => $user->type,
            'email' => $user->email,
            'personal_name' => $user->personal_name,
            'drive_school_name' => $user->drive_school_name,
            'registration_site' => $user->registration_site ,
            'trainingground_site' => $user->trainingground_site,
            'class_introduction' => $user->class_introduction,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}
