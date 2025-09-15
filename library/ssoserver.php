<?php
class ssoserver {

    public $salt = "45454564fgh4564rb456rnh4564"; // Salt for encryption
    private $method = "AES-256-CBC"; // Strong algorithm
    /**
     * Encrypt text with password
     */

    public function __construct() {
        // You can initialize client_id, auth_key, sso_server_url here if needed
    }

    public function validatePayload() {
        $encodedPayload = $_GET['payload'] ?? '';
        //echo '<br>';
        $hash = $_GET['hash'] ?? '';


        if(md5(urlencode($encodedPayload).$this->salt) === $hash) {
            $data = json_decode($this->decrypt($encodedPayload, $this->salt));
            
            $db = new  CRUD();
            $clients = $db->read('clients', "client_id='{$data->client_id}' and auth_key='{$data->auth_key}' and status='t'");
            
            if(count($clients) === 0) {
                return [
                    'status' => false,
                    'message' => 'Invalid client_id or auth_key'
                ];
            } else {
                return [
                    'status' => true,
                    'data' => $data->client = $clients[0]
                ];
            }
            
        } else {
            return [
                'status' => false,
                'message' => 'Invalid payload'
            ];
        }
    }

    public function checkUser($username, $password, $client_id, $redirect_url) {
        $db = new CRUD();
        $users = $db->read('users', "username='{$username}' and status='t'");
        if(count($users) === 0) {

            return [
                'status' => false,
                'message' => 'Invalid credencials. Please try again.'
            ];
        } else {
            if(password_verify($password, $users[0]['password'])) {
                //echo 'Login successful. Username: ' . $users[0]['username'];

                $authCode = hash('sha256', uniqid() . microtime());
                $insert_data = $db->create('authorizations', [
                    'user_id' => $users[0]['user_id'],
                    'client_id' => $client_id ?? null,
                    'auth_created_at' => date('Y-m-d H:i:s'),
                    'authorization_code' =>  $authCode,
                    'auth_validity_timestamp' => (int) microtime(true) + (5 * 60 * 1000) // 5 minutes from now

                ]);

                if( $insert_data){
                    $encodedAuthCode = $this->encrypt($authCode, $this->salt);
                    $callback_url = $redirect_url.'?token='.$encodedAuthCode.'&hash='.md5($encodedAuthCode . $this->salt);
                    header("Location: $callback_url");
                } else {
                    return [
                    'status' => false,
                    'message' => 'Invalid credencials. Please try again. (token gen failed)'
                ];
                }

                //print_r($insert);


            } else {
                return [
                    'status' => false,
                    'message' => 'Invalid credencials. Please try again. (password verify failed)'
                ];
            }
        }

        //return count($users) > 0 ? $users[0] : null;
    }

    public function encrypt($plaintext, $password) {
        // Generate a 16-byte random IV
        $iv = openssl_random_pseudo_bytes(16);

        // Derive a 256-bit key from the password using SHA-256
        $key = hash("sha256", $password, true);

        // Encrypt
        $ciphertext = openssl_encrypt($plaintext, $this->method, $key, OPENSSL_RAW_DATA, $iv);

        // Return base64(IV + ciphertext), URL encoded
        return urlencode(base64_encode($iv . $ciphertext));
    }

    /**
     * Decrypt text with password
     */
    public function decrypt($encrypted, $password) {
        $data = base64_decode($encrypted);

        // Extract IV (first 16 bytes)
        $iv = substr($data, 0, 16);

        // Extract ciphertext
        $ciphertext = substr($data, 16);

        // Derive key
        $key = hash("sha256", $password, true);

        // Decrypt
        return openssl_decrypt($ciphertext, $this->method, $key, OPENSSL_RAW_DATA, $iv);
    }
}
?>