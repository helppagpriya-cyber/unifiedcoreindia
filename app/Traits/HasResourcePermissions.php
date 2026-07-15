<?php

namespace App\Traits;

trait HasResourcePermissions
{
    abstract protected static function permissionPrefix(): string;


    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can(
            static::permissionPrefix() . '.view'
        ) ?? false;
    }


    public static function canViewAny(): bool
    {
        return auth()->user()?->can(
            static::permissionPrefix() . '.view'
        ) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can(
            static::permissionPrefix() . '.create'
        ) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(
            static::permissionPrefix() . '.update'
        ) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can(
            static::permissionPrefix() . '.delete'
        ) ?? false;
    }
}
