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

use BlueComet\Config\Database as DatabaseConfig;

class Database extends DatabaseConfig
{
    private static string $HOSTNAME;

    private static string $USERNAME;

    private static string $PASSWORD;

    private static string $DBNAME;

    private static string $CHARSET;

    private static string $COLLATION;

    protected $PDO = null;

    protected $STMT = null;

    /**
     * Returns an array of available PDO drivers.
     */
    public function getAvailableDrivers(): array
    {
        return \PDO::getAvailableDrivers();
    }

    public function __construct()
    {
        $this->isTesting();

        if ($this->defaultGroup === 'default') {
            self::$HOSTNAME  = $this->default['hostname'];
            self::$USERNAME  = $this->default['username'];
            self::$PASSWORD  = $this->default['password'];
            self::$DBNAME    = $this->default['database'];
            self::$CHARSET   = $this->default['DBCharset'];
            self::$COLLATION = $this->default['DBCollat'];
        }
        elseif($this->defaultGroup === 'tests') {
            self::$HOSTNAME  = $this->tests['hostname'];
            self::$USERNAME  = $this->tests['username'];
            self::$PASSWORD  = $this->tests['password'];
            self::$DBNAME    = $this->tests['database'];
            self::$CHARSET   = $this->tests['DBCharset'];
            self::$COLLATION = $this->tests['DBCollat'];
        }
    }

    /**
     * Connect to a database.
     */
    public function databaseConnect(string $dbname = ''): bool|string
    {
        $dbname = !empty($dbname) ? $dbname : self::$DBNAME;
        $SQL = 'mysql:host=' . self::$HOSTNAME . ';dbname=' . $dbname . ';charset=' . self::$CHARSET;
        try {
            $this->PDO = new \PDO($SQL, self::$USERNAME, self::$PASSWORD);
            
            $this->PDO->exec("SET NAMES '" . self::$CHARSET . "' COLLATE '" . self::$COLLATION . "'");
            $this->PDO->exec("SET CHARACTER SET '" . self::$CHARSET ."'");
            $this->PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->PDO->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            return true;
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * Close the database connection.
     */
    public function databaseClose(): void
    {
        $this->PDO = null;
        $this->STMT = null;
    }

    public function __destruct()
    {
        $this->PDO = null;
        $this->STMT = null;
    }
}
?>