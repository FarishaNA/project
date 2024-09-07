<?php
require_once '../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register($username, $email, $password, $role) {
        // Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->userModel->createUser($username, $email, $hashedPassword, $role);
    }

    public function login($email, $password) {
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }
}
