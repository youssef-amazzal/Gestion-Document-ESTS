<?php

namespace App\Enums;

enum Privileges : string
{
    /*************************************************************************************************
     *                                          important
     *
     * Space > parent folder > child folder > file
     * having a privilege on a space or a parent folder means having it on all its children
     *
     ************************************************************************************************/

    // System privileges
    case manage_users = 'system_manage_users'; // add, edit, delete users
    case manage_groups = 'system_manage_groups'; // add, edit, delete groups and group members [only owned groups];
    case Can_Upload = 'system_upload'; // those who can't upload are still able to make shortcuts to files


    // File privileges
    case View = 'file_view'; // view space or folder or download file
    case Upload_Into = 'file_upload_into'; // view + create folders or upload files into space or folder
    case Edit = 'file_edit'; // fullAccess : view + upload + delete + move + rename + change permissions + change tags

    public static function getType(Privileges|string $privilege): string
    {
        if ($privilege instanceof Privileges) {
            $privilege = $privilege->value;
        }
        return (str_starts_with($privilege, 'file_') ? 'file' : 'system');
    }

    public static function filePrivileges(): array
    {
        return array_filter(Privileges::cases(), fn($privilege) => self::getType($privilege) === 'file');
    }

    public static function systemPrivileges(): array
    {
        return array_filter(Privileges::cases(), fn($privilege) => self::getType($privilege) === 'system');
    }

    public static function getAdminPrivileges(): array
    {
        return array_filter(Privileges::cases(), fn($privilege) => self::getType($privilege) === 'system');
    }

    public static function getProfessorsPrivileges(): array
    {
        return [
            Privileges::manage_groups,
            Privileges::Can_Upload,
        ];
    }

    public static function getStudentsPrivileges(): array
    {
        return [];
    }
}
