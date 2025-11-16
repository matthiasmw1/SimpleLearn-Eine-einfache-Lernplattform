<?php
// util/users.php

$fakeUsers = [
    [
        'id' => 1,
        'username' => 'testUser',
        'email' => 'example@example.org',
        'password_hash' => '$2y$10$LQicULMMXeBtUmbGG3.O../BkKIDZNtfbuJYmP4FQRAS5NkAaFTiq', // denselben wie in login.php
        'role' => 'user'
    ]
];

function getAllUsers(): array
{
    global $fakeUsers;
    return $fakeUsers;
}

function findUserByUsernameOrEmail(string $usernameOrEmail): ?array
{
    global $fakeUsers;
    foreach ($fakeUsers as $u) {
        if ($u['username'] === $usernameOrEmail || $u['email'] === $usernameOrEmail) {
            return $u;
        }
    }
    return null;
}

function findUserById(int $id): ?array
{
    global $fakeUsers;
    foreach ($fakeUsers as $u) {
        if ((int)$u['id'] === $id) {
            return $u;
        }
    }
    return null;
}
