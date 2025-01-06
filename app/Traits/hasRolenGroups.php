<?php

namespace App\Traits;

trait hasRolenGroups
{
    public function isAdmin()
    {
        return $this->hasRole('ADMIN');
    }

    public function isUser()
    {
        return $this->groups()->where('name', '!=', 'ADMIN')->exists();
    }

    public function hasRole(string $role): bool
    {
        return $this->groups()->where('name', $role)->exists();
    }

    //apabila ada implementasi group dan role, tambahkan disini
}
