<?php

namespace app\controllers;

use Flight;

class ApiExampleController
{
    public function getUsers()
    {
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        Flight::json(['users' => $users]);
    }

    public function getUser($id)
    {
        $users = [
            1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            2 => ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        if (!isset($users[$id])) {
            Flight::notFound();
            return;
        }

        Flight::json(['user' => $users[$id]]);
    }

    public function updateUser($id)
    {
        $data = Flight::request()->data->getData();

        // Simple validation
        if (empty($data['name']) || empty($data['email'])) {
            Flight::json(['error' => 'Name and email are required'], 400);
            return;
        }

        // In a real app, you would update the database here
        $user = [
            'id' => (int)$id,
            'name' => $data['name'],
            'email' => $data['email'],
            'updated' => true
        ];

        Flight::json(['user' => $user]);
    }
}