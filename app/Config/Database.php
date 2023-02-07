<?php
namespace BlueComet\Config;

class Database
{
    /**
     * What is the group name of the database that will be used?
     */
    protected string $defaultGroup = 'default';

    /**
     * Database connection information.
     */
    protected array $default = [
        'hostname'  => 'localhost',
        'username'  => 'root',
        'password'  => '',
        'database'  => '',
        'DBCharset' => 'utf8mb4',
        'DBCollat'  => 'utf8mb4_general_ci'
    ];

    /**
     * Database connection information on localhost.
     */
    protected array $tests = [
        'hostname'  => 'localhost',
        'username'  => 'root',
        'password'  => '',
        'database'  => 'tests',
        'DBCharset' => 'utf8mb4',
        'DBCollat'  => 'utf8mb4_general_ci'
    ];

    protected function isTesting()
    {
        /**
         * When the environment is in 'testing' mode, it is expected 
         * that the database on localhost will be used.
         */
        if(ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
?>