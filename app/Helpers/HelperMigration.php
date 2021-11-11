<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class HelperMigration
 * @package App\Helpers
 */
class HelperMigration
{
    /**
     * Returns a list of foreign keys for a table.
     *
     * @param string $table
     * @return array|null
     */
    public static function listTableForeignKeys(string $table): ?array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }

    /**
     * @param string $table
     * @param string $uniqueKey
     * @return bool
     */
    public static function hasUniqueKeyInTable(string $table, string $uniqueKey): bool
    {
        $listUniqueKeys = [];
        $listRawUniqueKeys = DB::select(DB::raw("SHOW KEYS FROM $table WHERE Key_name='$uniqueKey'"));

        foreach ( $listRawUniqueKeys as $rawUniqueKey ) {
            $listUniqueKeys[] = $rawUniqueKey->Key_name;
        }

        return in_array($uniqueKey, $listUniqueKeys) ? true : false;
    }
}
