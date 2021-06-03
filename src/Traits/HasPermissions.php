<?php

namespace Sourceboat\Permission\Traits;

use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Check if model has all permissions
     *
     * @param string[]|string ...$permissions
     * @return bool
     */
    public function hasPermissionTo(...$permissions): bool
    {
        $rules = $this->getAllPermissions()
            ->map(static function (string $rule) {
                return explode('/', $rule);
            });

        $permissions = collect($permissions)
            ->flatten();

        return $permissions
            ->map(static function (string $permission) {
                return explode('/', $permission);
            })
            ->filter(function (array $permission) use ($rules) {
                return $this->matchPermission($rules, $permission);
            })->count() === $permissions->count();
    }

    /**
     * Check if model has all permissions
     *
     * @param string[]|string ...$permissions
     * @return bool
     */
    public function hasPermission(...$permissions): bool
    {
        return $this->hasPermissionTo($permissions);
    }

    /**
     * Check if model has any permissions
     *
     * @param string[]|string ...$permissions
     * @return bool
     */
    public function hasAnyPermission(...$permissions): bool
    {
        $rules = $this->getAllPermissions()
            ->map(static function (string $rule) {
                return explode('/', $rule);
            });

        return collect($permissions)
            ->flatten()
            ->map(static function (string $permission) {
                return explode('/', $permission);
            })
            ->filter(function (array $permission) use ($rules) {
                return $this->matchPermission($rules, $permission);
            })->count() > 0;
    }

    /**
     * Return current model permissions
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions(): Collection
    {
        $roles = config('permission.roles');
        $role = $this->{config('permission.column_name')};

        if ($role && array_key_exists($role, $roles)) {
            return collect($roles[$role]);
        }

        return collect([]);
    }

    /**
     * Match ruleset to permission
     *
     * @param \Illuminate\Support\Collection $rules Ruleset
     * @param string[] $permission
     * @return bool
     */
    public function matchPermission(Collection $rules, array $permission): bool
    {
        // match rules
        $matches = $rules->filter(function ($rule) use ($permission) {
            return $this->matchRuleToPermission($rule, $permission);
        });

        // match negative rule
        if ($matches->filter(static function ($rule) {
            return substr($rule[0], 0, 1) === '!';
        })->count() > 0) {
            return false;
        }

        // match positive rule
        return $matches->count() > 0;
    }

    /**
     * Match one rule to permission
     *
     * @param string[] $rule
     * @param string[] $permission
     * @return bool
     */
    public function matchRuleToPermission(array $rule, array $permission): bool
    {
        $rule[0] = str_replace('!', '', $rule[0]);

        $countRuleParts = count($rule);
        $countPermissionParts = count($permission);

        for ($i = 0; $i < $countPermissionParts; $i++) {
            // topic is longer, and no wildcard
            if ($i >= $countRuleParts) {
                return false;
            }

            // matched up to here, and now the wildcard says "all others will match"
            if ($rule[$i] === '#') {
                return true;
            }

            // text does not match, and there wasn't a + to excuse it
            if ($permission[$i] !== $rule[$i] && $rule[$i] !== '+') {
                return false;
            }
        }

        // make user/edit/# match user/edit
        if ($countPermissionParts === $countRuleParts - 1 && $rule[$countRuleParts - 1] === '#') {
            return true;
        }

        return $countPermissionParts === $countRuleParts;
    }
}
