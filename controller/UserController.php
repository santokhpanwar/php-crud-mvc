<?php

namespace controller;

use \model\User;
use \app\Controller;
use \app\Validator;  // Include the Validator class

class UserController extends Controller
{
    public function index($message = ''): void
    {
        $query = isset($_GET['q']) ? $_GET['q'] : '';

        $user = new User();

        $this->view('user/index', ['users' => $user->all($query), 'message' => $message]);
    }

    public function show($matches): void
    {
        $id = $matches['id'];
        
        $user = new User();

        $this->view('user/show', ['user' => $user->find($id)]);
    }

    public function create($data = []): void
    {
        $this->view('user/create', $data);
    }

    public function edit($matches): void
    {
        $id = $matches['id'];

        $user = new User();

        $this->view('user/update', ['user' => $user->find($id)]);
    }

    public function store(): void
    {
        // Validate input
        $validator = new Validator($_POST);
        $rules = [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];

        if ($validator->validate($rules)) {
            // Input is valid, proceed with user creation
            $user = new User();
            $user->create([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            ]);

            $this->create(['message' => 'User created successfully']);
        } else {
            // Validation failed, return errors
            $errors = $validator->getErrors();
            $this->create(['errors' => $errors]);
        }
    }

    public function delete($matches): void
    {
        $id = $matches['id'];
        
        $user = new User();
        
        $user->delete($id);
        
        $_SESSION['message'] = "User $id deleted successfully";

        header("Location: /users");
    }

    public function update($matches): void
    {
        $id = $matches['id'];

        // Validate input
        $validator = new Validator($_POST);
        $rules = [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
        ];

        if ($validator->validate($rules)) {
            // Input is valid, proceed with user update
            $user = new User();
            $user->update([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
            ], $id);

            header("Location: /users/$id");
        } else {
            // Validation failed, return errors
            $errors = $validator->getErrors();
            $this->edit(['errors' => $errors, 'id' => $id]);
        }
    }
}
