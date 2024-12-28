<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GoogleTOT extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('ItemModel');
    }
       
   public function index() { 
    require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
    $ga = new PHPGangsta_GoogleAuthenticator();

    // Generate a secret key for the user
    $secret = $ga->createSecret();
    
    // Generate a QR code URL
    $websiteName = 'Wefru India';
    $email = 'ashishranjan47b@gmail.com'; // Replace with user's email or identifier
    $qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteName . ':' . $email, $secret, $websiteName);

    // Pass the QR code URL and secret key to the view
    $data['qrCodeUrl'] = $qrCodeUrl;
    $data['secretKey'] = $secret;
    $this->load->view('setup_authenticator', $data);
}

// public function verify_authenticator() {
//     require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
//     $ga = new PHPGangsta_GoogleAuthenticator();

//     // Secret key would typically come from the database. For demo purposes:
//     $secret = $this->input->post('secret'); // Get the secret key from the AJAX request
//     $userInputCode = $this->input->post('otp'); // OTP entered by the user

//     if ($ga->verifyCode($secret, $userInputCode, 2)) { // 2 = 2*30sec clock tolerance
//         echo json_encode(['status' => 'success', 'message' => 'Authentication successful!']);
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'Authentication failed!']);
//     }
// }
public function verify_authenticator() {
    require_once(APPPATH . 'libraries/GoogleAuthenticator.php');
    $ga = new PHPGangsta_GoogleAuthenticator();

    $otp = $this->input->post('otp');
    $secret = $this->input->post('secret');

    if ($ga->verifyCode($secret, $otp, 2)) { // 2 = 2*30sec clock tolerance
        echo json_encode(['status' => 'success', 'message' => 'Authentication successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Authentication failed!']);
    }
}

}