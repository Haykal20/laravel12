<?php

namespace App\Policies;

use App\Models\Mahasiswa;
use App\Models\User;

class MahasiswaPolicy
{
    public function update(User $user, Mahasiswa $mahasiswa)
    {
        return $user->id === $mahasiswa->user_id;
    }

    public function delete(User $user, Mahasiswa $mahasiswa)
    {
        return $user->id === $mahasiswa->user_id;
    }
}
