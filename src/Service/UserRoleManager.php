<?php

namespace App\Service;

use App\Entity\User;

class UserRoleManager
{
    public function assignRoleBasedOnProfession(User $user): void
    {
        $roles = $user->getRoles();

        // Remove ROLE_PRODUCTEUR if it exists
        if (($key = array_search('ROLE_PRODUCTEUR', $roles)) !== false) {
            unset($roles[$key]);
        }

        // Add ROLE_PRODUCTEUR if profession is set
        if ($user->getProfession() !== null) {
            $roles[] = 'ROLE_PRODUCTEUR';
        }

        // Make sure ROLE_USER is always present
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        $user->setRoles(array_values(array_unique($roles)));
    }
}