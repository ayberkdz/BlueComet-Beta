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

// This class is being developed. Expected methods in the next update include; 
// table(),
// select(),
// from(),
// limit(),
// like(),
// where(),
// orderBy(),
// groupBy(), and more...

class Builder extends Database
{
    /**
     * Method used to avoid SQL injection when there are parameters in the query.
     */
    private function pQueryBuilder(string $query, array $params = []): mixed
    {
        if (is_null($params)) {
            $this->STMT = $this->PDO->query($query);
        }
        else {
            $this->STMT = $this->PDO->prepare($query);
            $this->STMT->execute($params);
        }
        return $this->STMT;
    }

    /**
     * Method used to add data to the database.
     */
    public function insert(string $query, array $params = []): mixed
    {
        try {
            $this->pQueryBuilder($query, $params);
            return $this->PDO->lastInsertId();
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * Method used to update data from the database.
     */
    public function update(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->rowCount();
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * Method used to delete data from the database.
     */
    public function delete(string $query, array $params = []): mixed
    {
        return $this->update($query, $params);
    }

    /**
     * This method is used to fetch all data (as objects) from the database and print it.
     */
    public function get(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->fetchAll();
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * This method is used to fetch all data (as an array) from the database and print it.
     */
    public function getArray(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * This method is used to fetch data (as objects) from the database and print it.
     */
    public function getRow(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->fetch();
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * This method is used to fetch data (as an array) from the database and print it.
     */
    public function getRowArray(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    /**
     * This is used to retrieve a single row and column data from the database and print it.
     */
    public function getColumn(string $query, array $params = []): mixed
    {
        try {
            return $this->pQueryBuilder($query, $params)->fetchColumn();
        } catch (\PDOException $e) {
            return die($e->getMessage());
        }
    }

    final public function __destruct()
    {
        $this->PDO = null;
        $this->STMT = null;
    }
}
?>