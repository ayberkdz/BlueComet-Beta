<?php
// MIT License
// Copyright (c) 2023 Ayberk Dönmez
//
// Permission is hereby granted, free of charge, to any person obtaining a copy of this software
// and associated documentation files (the "Software"), to deal in the Software without restriction,
// including without limitation the rights to use, copy, modify, merge, publish, distribute,
// sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all copies or
// substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
// BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
// IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
// OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


namespace BlueComet\Database;

use BlueComet\Database\Database;

class Forge extends Database
{
    /**
     * Check if a table exists in the database.
     */
    public function tableExists(string $tableName): bool
    {
        return $this->PDO->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0 ? true : false;
    }

    /**
     * Get a list of all tables in the database.
     */
    public function getTables(): array
    {
        return $this->PDO->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Create a new table in the database.
     */
    public function createTable(string $tableName, array $fields = [], array $attributes = [])
    {
        if(empty($fields)) {
            return false;
        }

        $query = "CREATE TABLE IF NOT EXISTS $tableName (";
        foreach ($fields as $ckey => $dprops) {
            $query .= "$ckey " . $dprops['type'];

            if (array_key_exists('length', $dprops)) {
                $query .= "(" . $dprops['length'] . ")";
            }

            if(array_key_exists('unsigned', $dprops) && $dprops['unsigned'] === true) {
                $query .= " UNSIGNED";
            }

            if(array_key_exists('null', $dprops) && !$dprops['null']) {
                $query .= " NOT NULL";
            }

            if (array_key_exists('default', $dprops)) {
                if(is_string($dprops['default']) && $dprops['default'] != 'CURRENT_TIMESTAMP') {
                    $query .= " DEFAULT '" . $dprops['default'] . "'";
                }
                if(! empty($dprops['default']) && $dprops['default'] == 'CURRENT_TIMESTAMP') {
                    $query .= " DEFAULT " . $dprops['default'];
                }
            }

            if(array_key_exists('unique', $dprops) && $dprops['unique'] === true) {
                $query .= " UNIQUE KEY";
            }

            if(array_key_exists('auto_increment', $dprops) && $dprops['auto_increment'] === true) {
                $query .= " AUTO_INCREMENT";
            }

            if(array_key_exists('primary', $dprops) && $dprops['primary'] === true) {
                $query .= " PRIMARY KEY";
            }

            $query .= ", ";
        }
        $query = rtrim($query, ', ');
        $query .= ")";

        if(!empty($attributes)) {
            if (array_key_exists('ENGINE', $attributes)) {
                $query .= " ENGINE = " . $attributes['ENGINE'];
            }
            if (! array_key_exists('ENGINE', $attributes)) {
                $query .= " ENGINE = MyISAM";
            }
    
            if (array_key_exists('CHARSET', $attributes)) {
                $query .= " DEFAULT CHARACTER SET " . $attributes['CHARSET'];
            }
    
            if (! array_key_exists('CHARSET', $attributes)) {
                $query .= " DEFAULT CHARACTER SET utf8mb4";
            }
    
            if (array_key_exists('COLLATE', $attributes)) {
                $query .= " DEFAULT CHARACTER SET " . $attributes['COLLATE'];
            }
    
            if (! array_key_exists('COLLATE', $attributes)) {
                $query .= " COLLATE utf8mb4_general_ci";
            }
        }

        return $this->PDO->query($query);
    }

    /**
     * Copy the structure of an existing table to a new table.
     */
    public function copyTable(string $existingTable, string $newTable)
    {
        if(! $this->tableExists($existingTable)) {
            return false;
        }
        return $this->PDO->query("CREATE TABLE IF NOT EXISTS $newTable LIKE $existingTable");
    }

    /**
     * Copy the structure and data of an existing table to a new table.
     */
    public function copyTableWithData(string $existingTable, string $newTable)
    {
        if(!$this->copyTable($existingTable, $newTable)) {
            return false;
        }
        return $this->PDO->query("INSERT INTO $newTable SELECT * FROM $existingTable");
    }

    /**
     * Rename an existing table.
     */
    public function renameTable(string $existingTable, string $newTable)
    {
        if(! $this->tableExists($existingTable)) {
            return false;
        }
        if($this->tableExists($newTable)) {
            return false;
        }
        return $this->PDO->query("RENAME TABLE $existingTable TO $newTable");
    }

    /**
     * Truncate (empty) an existing table.
     */
    public function truncateTable(string $tableName)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }
        return $this->PDO->query("TRUNCATE TABLE $tableName");
    }

    /**
     * Drop (delete) an existing table.
     */
    public function dropTable(string $tableName)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }
        return $this->PDO->query("DROP TABLE $tableName");
    }

    /**
     * Check if a field exists in a table.
     */
    public function fieldExists(string $tableName, string $fieldName)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }
        return $this->PDO->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->rowCount() > 0 ? true : false;
    }

    /**
     * Get a list of all fields in a table.
     */
    public function getTableFields(string $tableName): bool|array
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }
        return $this->PDO->query("SHOW COLUMNS FROM $tableName")->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Add a new field to a table.
     */
    public function addFields(string $tableName, array|string $fields)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }

        $query = "ALTER TABLE $tableName";

        if(is_string($fields)) {
            $query .= " ADD COLUMN $fields";
        }
        elseif(is_array($fields)) {
            foreach ($fields as $ckey => $dprops) {
                if($this->fieldExists($tableName, $ckey)) {
                    return false;
                }
            }

            foreach ($fields as $ckey => $dprops) {
                $query .= " ADD COLUMN $ckey " . $dprops['type'];
    
                if (array_key_exists('length', $dprops)) {
                    $query .= "(" . $dprops['length'] . ")";
                }
    
                if(array_key_exists('unsigned', $dprops) && $dprops['unsigned'] === true) {
                    $query .= " UNSIGNED";
                }
    
                if(array_key_exists('null', $dprops) && !$dprops['null']) {
                    $query .= " NOT NULL";
                }
    
                if (array_key_exists('default', $dprops)) {
                    if(is_string($dprops['default']) && $dprops['default'] != 'CURRENT_TIMESTAMP') {
                        $query .= " DEFAULT '" . $dprops['default'] . "'";
                    }
                    if(! empty($dprops['default']) && $dprops['default'] == 'CURRENT_TIMESTAMP') {
                        $query .= " DEFAULT " . $dprops['default'];
                    }
                }
    
                if(array_key_exists('unique', $dprops) && $dprops['unique'] === true) {
                    $query .= " UNIQUE KEY";
                }
    
                if(array_key_exists('auto_increment', $dprops) && $dprops['auto_increment'] === true) {
                    $query .= " AUTO_INCREMENT";
                }
    
                if(array_key_exists('primary', $dprops) && $dprops['primary'] === true) {
                    $query .= " PRIMARY KEY";
                }
    
                if(array_key_exists('after', $dprops)) {
                    $query .= " AFTER " . $dprops['after'];
                }
    
                if(array_key_exists('first', $dprops) && $dprops['first'] === true) {
                    $query .= " FIRST";
                }
    
                $query .= ", ";
            }
            $query = rtrim($query, ', ');
        }
        return $this->PDO->query($query);
    }

    /**
     * Change the definition of an existing field in a table.
     */
    public function changeFields(string $tableName, string|array $fields, array $changeFields = [])
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }

        $query = "ALTER TABLE $tableName";

        if(is_string($fields) && empty($changeFields)) {
            $query .= " CHANGE $fields";
        }
        elseif(is_array($fields) && is_array($changeFields)) {
            foreach ($fields as $dkey => $dprops) {
                if(! $this->fieldExists($tableName, $dkey)) {
                    return false;
                }
            }

            foreach ($changeFields as $ckey => $dprops) {
                if($this->fieldExists($tableName, $ckey)) {
                    return false;
                }
            }

            foreach ($fields as $dkey => $dprops) {
                foreach ($changeFields as $cckey => $cprops) {
                    
                    if ($cprops['fieldName'] === $dkey) {
                        $query .= " CHANGE $dkey $cckey " . $cprops['type'];
                        
                        if (array_key_exists('length', $cprops)) {
                            $query .= "(" . $cprops['length'] . ")";
                        }

                        if(array_key_exists('unsigned', $cprops) && $cprops['unsigned'] === true) {
                            $query .= " UNSIGNED";
                        }
            
                        if(array_key_exists('null', $cprops) && !$cprops['null']) {
                            $query .= " NOT NULL";
                        }

                        if (array_key_exists('default', $cprops)) {
                            if(is_string($cprops['default']) && $cprops['default'] != 'CURRENT_TIMESTAMP') {
                                $query .= " DEFAULT '" . $cprops['default'] . "'";
                            }
                            if(! empty($cprops['default']) && $cprops['default'] == 'CURRENT_TIMESTAMP') {
                                $query .= " DEFAULT " . $cprops['default'];
                            }
                        }
            
                        if(array_key_exists('unique', $cprops) && $cprops['unique'] === true) {
                            $query .= " UNIQUE KEY";
                        }

                        if(array_key_exists('auto_increment', $cprops) && $cprops['auto_increment'] === true) {
                            $query .= " AUTO_INCREMENT";
                        }
            
                        if(array_key_exists('primary', $cprops) && $cprops['primary'] === true) {
                            $query .= " PRIMARY KEY";
                        }
            
                        if(array_key_exists('after', $cprops)) {
                            $query .= " AFTER " . $cprops['after'];
                        }

                        if(array_key_exists('first', $cprops) && $cprops['first'] === true) {
                            $query .= " FIRST";
                        }
                    }
                }

                $query .= ", ";
            }
        }
        
        $query = rtrim($query, ', ');
        return $this->PDO->query($query);
    }

    /**
     * Modify the definition of an existing field in a table.
     */
    public function modifyFields(string $tableName, string|array $modifyFields)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }

        $query = "ALTER TABLE $tableName";

        if(is_string($modifyFields)) {
            $query .= " MODIFY $modifyFields";
        }
        elseif(is_array($modifyFields)) {
            foreach ($modifyFields as $ckey => $dprops) {
                if(! $this->fieldExists($tableName, $ckey)) {
                    return false;
                }
            }

            foreach ($modifyFields as $ckey => $dprops) {
                $query .= " MODIFY $ckey " . $dprops['type'];
    
                if (array_key_exists('length', $dprops)) {
                    $query .= "(" . $dprops['length'] . ")";
                }
    
                if(array_key_exists('unsigned', $dprops) && $dprops['unsigned'] === true) {
                    $query .= " UNSIGNED";
                }
    
                if(array_key_exists('null', $dprops) && !$dprops['null']) {
                    $query .= " NOT NULL";
                }
    
                if (array_key_exists('default', $dprops)) {
                    if(is_string($dprops['default']) && $dprops['default'] != 'CURRENT_TIMESTAMP') {
                        $query .= " DEFAULT '" . $dprops['default'] . "'";
                    }
                    if(! empty($dprops['default']) && $dprops['default'] == 'CURRENT_TIMESTAMP') {
                        $query .= " DEFAULT " . $dprops['default'];
                    }
                }
    
                if(array_key_exists('unique', $dprops) && $dprops['unique'] === true) {
                    $query .= " UNIQUE KEY";
                }
    
                if(array_key_exists('auto_increment', $dprops) && $dprops['auto_increment'] === true) {
                    $query .= " AUTO_INCREMENT";
                }
    
                if(array_key_exists('primary', $dprops) && $dprops['primary'] === true) {
                    $query .= " PRIMARY KEY";
                }
    
                if(array_key_exists('after', $dprops)) {
                    $query .= " AFTER " . $dprops['after'];
                }
    
                if(array_key_exists('first', $dprops) && $dprops['first'] === true) {
                    $query .= " FIRST";
                }
    
                $query .= ", ";
            }
        }
        $query = rtrim($query, ', ');
        return $this->PDO->query($query);
    }

    /**
     * Drop the column.
     */
    public function dropField(string $tableName, string $field)
    {
        if(! $this->tableExists($tableName)) {
            return false;
        }

        if(! $this->fieldExists($tableName, $field)) {
            return false;
        }

        return $this->PDO->query("ALTER TABLE $tableName DROP COLUMN $field");
    }

    /**
     * Perform maintenance on a table
     */
    public function maintenance(bool $getStatus = false): bool|array
    {
        $tables = $this->PDO->query("SHOW TABLES");
        $tables->setFetchMode(\PDO::FETCH_NUM);
        if($tables->rowCount() > 0) {
            $result = [];
            foreach ($tables as $items) {
                $check = $this->PDO->query("CHECK TABLE " . $items[0]);
                $analyze = $this->PDO->query("ANALYZE TABLE " . $items[0]);
                $repair = $this->PDO->query("REPAIR TABLE " . $items[0]);
                $optimize = $this->PDO->query("OPTIMIZE TABLE " . $items[0]);
                if ($check == true && $analyze == true && $repair == true && $optimize == true) {
                    if(! $getStatus) {
                        return true;
                    }
                    array_push($result, $items[0]);
                }
            }
            return $result ?? true;
        }
        return false;
    }

    final public function __destruct()
    {
        $this->PDO = null;
    }
}
?>