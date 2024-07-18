<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Models\UserModel;
use App\Models\AccessModel;
use CodeIgniter\Controller;

class Main extends BaseController
{
    protected $session;
    protected $data;
    protected $crud_model;
    protected $user_model;
    protected $access_model;

    public function __construct()
    {
        $this->crud_model = new CrudModel();
        $this->user_model = new UserModel();
        $this->access_model = new AccessModel();
        $this->session = \Config\Services::session();
        $this->data['session'] = $this->session;
    }

    // Home Page
    public function index()
    {
        $data = [
            'page_title' => 'Home Page',
            'session' => $this->session
        ];

        echo view('templates/header', $data);
        echo view('crud/home');
        echo view('templates/footer');
    }

    // Create Form Page
    public function create()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        $this->data['page_title'] = "Add New";
        $this->data['request'] = $this->request;
        echo view('templates/header', $this->data);
        echo view('crud/create', $this->data);
        echo view('templates/footer');
    }

    // Insert And Update Function
    public function save()
    {
        if ($this->request->isAJAX()) {
            $request = service('request');

            // reCAPTCHA secret key
            $recaptchaSecret = '6LfWkv0pAAAAAG1YkzNEejn3dSwsFzSuxNZs0OQn';

            // reCAPTCHA response from the form
            $recaptchaResponse = $request->getPost('g-recaptcha-response');

            // Verify the reCAPTCHA response
            $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $response = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
            $responseKeys = json_decode($response, true);

            // Check if reCAPTCHA validation was successful
            if (intval($responseKeys["success"]) !== 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please complete the reCAPTCHA'
                ]);
            }

            // Define validation rules
            $validation = \Config\Services::validation();
            $validation->setRules([
                'firstname' => 'required|alpha|min_length[2]',
                'lastname' => 'required|alpha|min_length[2]',
                'gender' => 'required|in_list[Male,Female]',
                'contact' => 'required|numeric|exact_length[10]',
                'email' => 'required|valid_email',
                'address' => 'required|string|min_length[5]',
                'g-recaptcha-response' => 'required'
            ]);

            // Validate form data
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $validation->getErrors()
                ]);
            }

            // Capture the form data
            $post = [
                'firstname' => $this->request->getPost('firstname'),
                'middlename' => $this->request->getPost('middlename'),
                'lastname' => $this->request->getPost('lastname'),
                'gender' => $this->request->getPost('gender'),
                'contact' => $this->request->getPost('contact'),
                'email' => $this->request->getPost('email'),
                'address' => $this->request->getPost('address')
            ];

            // Save or update the data
            if (!empty($this->request->getPost('id'))) {
                $save = $this->crud_model->update($this->request->getPost('id'), $post);
                $message = 'Data has been updated successfully';
            } else {
                $save = $this->crud_model->insert($post);
                $message = 'Data has been added successfully';
            }

            // Check if data was saved successfully
            if ($save) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while saving the data'
                ]);
            }
        }

        return redirect()->back();
    }

    // List Page
    public function list()
    {
        try {
            $this->data['page_title'] = "List of Contacts";
            $this->data['list'] = $this->crud_model->orderBy('date_created', 'ASC')->findAll();
            echo view('templates/header', $this->data);
            echo view('crud/list', $this->data);
            echo view('templates/footer');
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Access Control Function
    private function checkAccess($username, $action)
    {
        $accessList = [
            'hello' => ['view', 'edit', 'delete'],
            'dipesh' => []
        ];

        return in_array($action, $accessList[$username]);
    }

    // Log Actions
    private function logAction($username, $action)
    {
        $data = [
            'name' => $username,
            'users' => $username,
            'access' => $action,
        ];

        $this->access_model->insert($data);
    }

    // View Function
    public function view_details($id = '')
    {
        if (empty($id)) {
            $this->session->setFlashdata('error_message', 'Unknown Data ID.');
            return redirect()->to('/list');
        }
        $username = $this->session->get('username');
        if ($this->checkAccess($username, 'view')) {
            $this->data['page_title'] = "View Contact Details";
            $qry = $this->crud_model->select("*, CONCAT(lastname,', ',firstname,COALESCE(concat(' ', middlename), '')) as `name`")->where(['id' => $id]);
            $this->data['data'] = $qry->first();
            $this->logAction($username, 'view');
            echo view('templates/header', $this->data);
            echo view('crud/view', $this->data);
            echo view('templates/footer');
        } else {
            $this->session->setFlashdata('error_message', 'Access Denied');
            return redirect()->to('/');
        }
    }

    // Edit Function
    public function edit($id = '')
    {
        if (empty($id)) {
            $this->session->setFlashdata('error_message', 'Unknown Data ID.');
            return redirect()->to('/list');
        }
        $username = $this->session->get('username');
        if ($this->checkAccess($username, 'edit')) {
            $this->data['page_title'] = "Edit Contact Details";
            $qry = $this->crud_model->select('*')->where(['id' => $id]);
            $this->data['data'] = $qry->first();
            $this->logAction($username, 'edit');
            echo view('templates/header', $this->data);
            echo view('crud/edit', $this->data);
            echo view('templates/footer');
        } else {
            $this->session->setFlashdata('error_message', 'Access Denied');
            return redirect()->to('/');
        }
    }

    // Delete Function
    public function delete($id = '')
    {
        if (empty($id)) {
            $this->session->setFlashdata('error_message', 'Unknown Data ID.');
            return redirect()->to('/list');
        }
        $username = $this->session->get('username');
        if ($this->checkAccess($username, 'delete')) {
            $delete = $this->crud_model->delete($id);
            $this->logAction($username, 'delete');
            if ($delete) {
                $this->session->setFlashdata('success_message', 'Contact Details has been deleted successfully.');
                return redirect()->to('/list');
            }
        } else {
            $this->session->setFlashdata('error_message', 'Access Denied');
            return redirect()->to('/');
        }
    }

    // Register Page
    public function register()
    {
        helper(['form']);
        $this->data['page_title'] = "Register";

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[255]|is_unique[users.username]',
                'email' => 'required|min_length[6]|max_length[255]|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]|max_length[255]'
            ];

            if ($this->validate($rules)) {
                $newData = [
                    'username' => $this->request->getPost('username'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                ];

                $this->user_model->save($newData);
                return redirect()->to('/login');
            } else {
                $this->data['validation'] = $this->validator;
            }
        }

        echo view('templates/header', $this->data);
        echo view('crud/register', $this->data);
        echo view('templates/footer');
    }

    // Login Page
    public function login()
    {
        helper(['form']);
        $this->data['page_title'] = "Login";

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[255]',
                'password' => 'required|min_length[8]|max_length[255]'
            ];

            if ($this->validate($rules)) {
                $user = $this->user_model->where('username', $this->request->getPost('username'))->first();

                if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
                    $this->session->set('isLoggedIn', true);
                    $this->session->set('username', $user['username']);
                    return redirect()->to('/create'); // Redirect to add new page if logged in
                } else {
                    $this->data['validation'] = $this->validator->setError('password', 'Username or Password is incorrect');
                }
            } else {
                $this->data['validation'] = $this->validator;
            }
        }

        echo view('templates/header', $this->data);
        echo view('crud/login', $this->data);
        echo view('templates/footer');
    }

    // Logout
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
