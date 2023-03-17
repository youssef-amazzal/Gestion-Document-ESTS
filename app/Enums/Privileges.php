<?php

namespace App\Enums;

enum Privileges : string
{
    // File privileges
    case FileCreate = 'file_create';
    case FileRead = 'file_read';
    case FileUpdate = 'file_update';
    case FileDelete = 'file_delete';
    case FileShare = 'file_share';
    case FileDownload = 'file_download';
    case FileUpload = 'file_upload';

    // System privileges
    case SystemGrant = 'system_grant';
    case SystemRevoke = 'system_revoke';
    case SystemCreateUser = 'system_create_user';
    case SystemEditUser = 'system_edit_user';
    case SystemDeleteUser = 'system_delete_user';
    case SystemCreateGroup = 'system_create_group';
    case SystemDeleteGroup = 'system_delete_group';
    case SystemAddToGroup = 'system_add_to_group';
    case SystemRemoveUserFromGroup = 'system_remove_from_group';
    case SystemBackup = 'system_backup';
    case SystemRestore = 'system_restore';

}
