<?php
require_once FCPATH . 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

class GoogleDriveController extends CI_Controller {

    private $client;
    private $service;

    public function __construct() {
        parent::__construct();

        // Load Google API Client
        $this->load->library('session');
        $this->client = new Client();
        $this->client->setAuthConfig(APPPATH . 'config/credentials.json');
        $this->client->addScope(Drive::DRIVE);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        
        // Initialize Drive service
        $this->service = new Drive($this->client);
    }

    // Step 1: Authenticate user
    public function authenticate() {
        if (!$this->client->isAccessTokenExpired()) {
            // Already authenticated
            return redirect('googleDriveController/createFolder');
        }

        // Generate authentication URL
        $authUrl = $this->client->createAuthUrl();
        redirect($authUrl);
    }

    // Step 2: Google OAuth callback
    public function callback() {
        if (isset($_GET['code'])) {
            $authCode = $_GET['code'];
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
            $this->client->setAccessToken($accessToken);

            // Save access token to session for future use
            $this->session->set_userdata('access_token', $accessToken);
            return redirect('googleDriveController/createFolder');
        }
    }

    // Step 3: Create folders on Google Drive for the multi-company structure
    public function createFolder() {
        // Get the access token from the session
        if ($this->session->userdata('access_token')) {
            $this->client->setAccessToken($this->session->userdata('access_token'));
        }

        if ($this->client->isAccessTokenExpired()) {
            return redirect('googleDriveController/authenticate');
        }

        // Define parent company and child company IDs
        $parentCompanyIds = [667, 671,679];   // Example parent companies
        $childCompanyIds = [
            667 => [668, 669, 670],   // Children of parent company 667
            671 => [672, 673, 674],  // Children of parent company 671
            679 => [672, 673, 674],  // Children of parent company 679
        ];

        foreach ($parentCompanyIds as $parentId) {
            // Step 4: Create the main folder for the parent company
            $parentFolder = $this->createFolderOnDrive($parentId);

            if ($parentFolder === null) {
                log_message('error', 'Failed to create parent folder for Company_' . $parentId);
                continue; // Skip to next parent company if folder creation fails
            }

            if (isset($childCompanyIds[$parentId])) {
                // Step 5: Create subfolders for child companies
                foreach ($childCompanyIds[$parentId] as $childId) {
                    $this->createFolderOnDrive($childId, $parentFolder['id']);
                }
            }
        }
    }

    // Step 6: Create folder on Google Drive
    private function createFolderOnDrive($companyId, $parentId = null) {
        // Prepare folder metadata
        $folderMetadata = new Drive\DriveFile();
        $folderMetadata->setName("Company_$companyId");
        $folderMetadata->setMimeType('application/vnd.google-apps.folder');

        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }

        // Step 7: Create folder on Google Drive
        try {
            $folder = $this->service->files->create($folderMetadata, [
                'fields' => 'id'
            ]);

            // Log the folder creation success and ID
            log_message('info', 'Folder created successfully for Company_' . $companyId . ': ' . $folder->getId());

            // Return the folder details
            return $folder;
        } catch (Exception $e) {
            // Log the error if folder creation fails
            log_message('error', 'Error creating folder for Company_' . $companyId . ': ' . $e->getMessage());
            return null;
        }
    }

    // Step 8: Upload a file to a specific folder
    public function uploadFile($filePath, $folderId) {
        // Check if the user is authenticated
        if ($this->client->isAccessTokenExpired()) {
            return redirect('googleDriveController/authenticate');
        }

        // Prepare file metadata
        $fileMetadata = new Drive\DriveFile();
        $fileMetadata->setName(basename($filePath));  // File name will be the file's base name
        $fileMetadata->setParents([$folderId]);

        // Upload the file content
        $content = file_get_contents($filePath);
        try {
            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => mime_content_type($filePath),  // Dynamically set MIME type
                'uploadType' => 'multipart'
            ]);
            return $file;
        } catch (Exception $e) {
            log_message('error', 'Error uploading file to folder ' . $folderId . ': ' . $e->getMessage());
            return null;
        }
    }
}