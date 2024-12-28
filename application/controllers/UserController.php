<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// UserController.php
class UserController extends CI_Controller {

    // Display the login page
    public function index() {
        $this->load->view('login'); // Load login view
    }
 // Registration method
public function register() {
    if ($this->input->post()) {
        // Basic user data (username, email, password)
        $username = $this->input->post('username');
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $email = $this->input->post('email');

        // Generate secret key for 2FA
        require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret(); // Create a secret key

        // Store the user data in the database, including the secret key (don't enable OTP just yet)
        $this->db->insert('users', [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'auth_secret' => $secret,
            'is_otp_enabled' => 0 // Not yet enabled for OTP
        ]);

        // Get user ID of the inserted user
        $userId = $this->db->insert_id();

        // Generate QR code URL for Google Authenticator
        $websiteName = 'Wefru-India';
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteName . ':' . $email, $secret, $websiteName);

        // Return QR code and secret to the front-end
        echo json_encode(['status' => 'otp_required', 'user_id' => $userId, 'qr_code_url' => $qrCodeUrl, 'secret' => $secret]);
    } else {
        // If no post data, load the registration view
        $data = [
            'userId' => null,  // Set to null or handle as needed
            'secret' => null    // Set to null or handle as needed
        ];

        // Load the registration view and pass the user data
        $this->load->view('register', $data);
    }
}

    

    // Login method
 public function login() {
    if ($this->input->post()) {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Verify user credentials
        $user = $this->db->get_where('users', ['username' => $username])->row();

        if ($user && password_verify($password, $user->password)) {
            // Store user in session
            $this->session->set_userdata('user_id', $user->id);

            // If OTP is enabled, ask for OTP verification
            if ($user->is_otp_enabled) {
                echo json_encode(['status' => 'otp_required']); // OTP required for this user
            } else {
                $this->session->set_userdata('is_logged_in', true);
                echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    } else {
        $this->load->view('login'); // Load login page if no post data
    }
}
 
public function verify_otp() {
    $otp = $this->input->post('otp');
    $userId = $this->input->post('user_id');

    require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
    $ga = new PHPGangsta_GoogleAuthenticator();

    // Get the user's secret key
    $user = $this->db->get_where('users', ['id' => $userId])->row();
    $secret = $user->auth_secret;

    // Verify OTP
    if ($ga->verifyCode($secret, $otp, 2)) {
        // OTP is correct, mark user as logged in and redirect to dashboard
        $this->session->set_userdata('is_logged_in', true);
        echo json_encode(['status' => 'success', 'message' => 'OTP verified. Welcome!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
    }
}


    // OTP Setup method (for 2FA)
    public function setup_otp() {
        $userId = $this->session->userdata('user_id');
        if (!$userId) {
            redirect('login');
        }

        require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
        $ga = new PHPGangsta_GoogleAuthenticator();

        // Get user details and generate the QR code for 2FA
        $user = $this->db->get_where('users', ['id' => $userId])->row();
        if (!$user->auth_secret) {
            $secret = $ga->createSecret(); // Generate new secret if not already set
            $this->db->update('users', ['auth_secret' => $secret], ['id' => $userId]);
        } else {
            $secret = $user->auth_secret; // Retrieve the existing secret
        }

        // Generate QR code URL for Google Authenticator or any compatible app
        $websiteName = 'WEFRU';
        $email = $user->email;
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteName . ':' . $email, $secret, $websiteName);

        $data['qrCodeUrl'] = $qrCodeUrl;
        $data['secretKey'] = $secret;
        $this->load->view('setup_authenticator', $data); // Load the view to display QR code
    }

    // OTP Verification method
 public function verify_otpR() {
    $otp = $this->input->post('otp');
    $userId = $this->input->post('user_id');
    $secret = $this->input->post('secret');

    require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
    $ga = new PHPGangsta_GoogleAuthenticator();

    // Verify the OTP entered by the user
    if ($ga->verifyCode($secret, $otp, 2)) {
        // OTP is correct, now enable OTP for the user
        $this->db->update('users', ['is_otp_enabled' => 1], ['id' => $userId]);

        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
    }
}

}