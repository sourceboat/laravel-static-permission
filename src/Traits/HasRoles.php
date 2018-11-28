<?php

namespace Sourceboat\Permission\Traits;

trait HasRoles
{
    use HasPermissions;

    /**
     * Assign the role to the model
     *
     * @param  string $role rolename
     * @return $this
     */
    public function assignRole(string $role)
    {
        $roles = config('permission.roles');

        if (array_key_exists($role, $roles)) {
            $this->{config('permission.column_name')} = $role;

            if ($this->exists) {
                $this->save();
            }
        }

        return $this;
    }

    /**
     * Compare role with given model role
     *
     * @param  string  $role role name
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->{config('permission.column_name')} === $role;
    }

    /**
     * Return current role name
     *
     * @return string|null
     */
    public function getRoleName(): ?string
    {
        return $this->{config('permission.column_name')};
    }
}
