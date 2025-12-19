<?php

namespace controllers;

use core\Controller;
use models\User;

/**
 * Auth Controller
 */
class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Login page
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }

        $this->render('auth/login');
    }

    /**
     * Handle login
     */
    private function handleLogin()
    {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($username) || empty($password)) {
            $data = ['error' => 'Vui lòng nhập tên đăng nhập và mật khẩu'];
            return $this->render('auth/login', $data);
        }

        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            $data = ['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác'];
            return $this->render('auth/login', $data);
        }

        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin' || $user['role'] === 'staff') {
            $this->redirect(APP_URL . '/admin');
        }

        $this->redirect(APP_URL);
    }

    /**
     * Register page
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleRegister();
        }

        $this->render('auth/register');
    }

    /**
     * Handle register
     */
    private function handleRegister()
    {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';

        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Tên đăng nhập phải ít nhất 3 ký tự';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải ít nhất 6 ký tự';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu không khớp';
        }

        if (empty($fullName)) {
            $errors[] = 'Tên đầy đủ không được để trống';
        }

        if ($this->userModel->getByUsername($username)) {
            $errors[] = 'Tên đăng nhập đã tồn tại';
        }

        if ($this->userModel->getByEmail($email)) {
            $errors[] = 'Email đã tồn tại';
        }

        if (!empty($errors)) {
            $data = [
                'errors' => $errors,
                'username' => $username,
                'email' => $email,
                'full_name' => $fullName
            ];
            return $this->render('auth/register', $data);
        }

        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName,
            'role' => 'customer'
        ];

        $this->userModel->createUser($userData);
        $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
        $this->redirect(APP_URL . '/auth/login');
    }

    /**
     * Logout
     */
    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['cart']);
        unset($_SESSION['promotion']);
        session_destroy();
        $this->redirect(APP_URL);
    }
}
