<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Signed Request Toolkit - Handles cryptographically signed HTTP requests
 *
 * Provides secure communication between client and server using public/private key pairs
 * for request signing and verification.
 */
class SignedRequestTk
{
    // cURL configuration
    public bool $curl_ignoressl = false;
    public int $curl_timeout = 10;
    public array $curl_opts = [];
    public ?array $curl_headers = null;

    // Client data
    public ?string $signature = null;

    // Server response
    public int $http_code = 0;
    public ?string $response = null;
    public bool $is_json = false;

    // Private properties requiring validation
    private ?string $_url = null;
    private ?string $_privkey = null;
    private ?string $_pubkey = null;
    private string $_algo = 'sha512';

    /**
     * Constructor
     *
     * @param string|null $url Target URL for requests
     * @param bool $allow_insecure Allow non-HTTPS URLs (not recommended)
     * @throws Exception
     */
    public function __construct(?string $url = null, bool $allow_insecure = false)
    {
        // Verify required PHP extensions
        $required_functions = ['openssl_get_md_methods', 'openssl_sign', 'openssl_verify', 'curl_init'];

        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                throw new \RuntimeException("Required PHP function {$function} is not available");
            }
        }

        if ($url !== null) {
            $this->setUrl($url, $allow_insecure);
        }
    }


    /**
     * Set the target URL for requests
     *
     * @param string $url Target URL
     * @param bool $allow_insecure Allow non-HTTPS URLs
     * @return self
     * @throws InvalidArgumentException
     */
    public function setUrl(string $url, bool $allow_insecure = false): self
    {
        if (!$allow_insecure && !str_starts_with($url, 'https://')) {
            throw new \InvalidArgumentException("URL must be HTTPS ({$url})");
        }

        $this->_url = $url;
        return $this;
    }

    /**
     * Set the signature algorithm
     *
     * @param string $algo Algorithm name (must be supported by OpenSSL)
     * @return self
     * @throws InvalidArgumentException
     */
    public function setAlgorithm(string $algo): self
    {
        if (!in_array($algo, openssl_get_md_methods(), true)) {
            throw new \InvalidArgumentException("Invalid signature algorithm '{$algo}'");
        }

        $this->_algo = $algo;
        return $this;
    }

    /**
     * Configure cURL to ignore SSL certificate verification
     *
     * @param bool $ignore Whether to ignore SSL verification
     * @return self
     */
    public function setCurlIgnoreSSL(bool $ignore): self
    {
        $this->curl_ignoressl = $ignore;
        return $this;
    }

    /**
     * Set cURL timeout in seconds
     *
     * @param int $seconds Timeout in seconds
     * @return self
     */
    public function setCurlTimeout(int $seconds): self
    {
        $this->curl_timeout = max(1, $seconds);
        return $this;
    }

    /**
     * Set additional cURL options
     *
     * @param array $options Array of CURLOPT_* constants and values
     * @return self
     */
    public function setCurlOptions(array $options): self
    {
        foreach ($options as $key => $value) {
            $this->curl_opts[$key] = $value;
        }
        return $this;
    }

    /**
     * Set HTTP headers for cURL requests
     *
     * @param array|string $headers HTTP headers
     * @return self
     */
    public function setCurlHeaders(array|string $headers): self
    {
        $this->curl_headers = is_array($headers) ? $headers : [$headers];
        return $this;
    }



    // =====================================
    // Key Management Methods
    // =====================================

    /**
     * Set private key from string
     *
     * @param string $data Private key in PEM format
     * @return self
     * @throws InvalidArgumentException
     */
    public function setPrivateKey(string $data): self
    {
        $this->validatePrivateKey($data);
        return $this;
    }

    /**
     * Set public key from string
     *
     * @param string $data Public key in PEM format
     * @return self
     * @throws InvalidArgumentException
     */
    public function setPublicKey(string $data): self
    {
        $this->validatePublicKey($data);
        return $this;
    }

    /**
     * Load private key from file
     *
     * @param string $filename Path to private key file
     * @return self
     * @throws Exception
     */
    public function loadPrivateKeyFile(string $filename): self
    {
        $data = $this->validateKeyFile($filename);
        $this->validatePrivateKey($data);
        return $this;
    }

    /**
     * Load public key from file
     *
     * @param string $filename Path to public key file
     * @return self
     * @throws Exception
     */
    public function loadPublicKeyFile(string $filename): self
    {
        $data = $this->validateKeyFile($filename);
        $this->validatePublicKey($data);
        return $this;
    }



    // =====================================
    // Client Methods
    // =====================================

    /**
     * Generate signature for payload
     *
     * @param string $payload Data to sign
     * @return void
     * @throws RuntimeException
     */
    public function generateSignature(string $payload): void
    {
        if (!$this->_privkey) {
            throw new \RuntimeException('Private key not set');
        }

        if (!openssl_sign($payload, $signature, $this->_privkey, $this->_algo)) {
            throw new \RuntimeException('Failed to generate signature');
        }

        $this->signature = $signature;
    }

    /**
     * Send signed HTTP request
     *
     * @param string $payload Data to send
     * @param string $payload_name Form field name for payload
     * @param string $signature_name Form field name for signature
     * @return void
     * @throws Exception
     */
    public function sendRequest(string $payload, string $payload_name = 'payload', string $signature_name = 'signature'): void
    {
        // Reset response state
        $this->http_code = 0;
        $this->response = null;
        $this->is_json = false;

        // Validate client configuration
        $this->validateClientConfig($payload);

        // Generate signature
        $this->generateSignature($payload);

        // Prepare request data
        $request = [
            $payload_name => $payload,
            $signature_name => base64_encode($this->signature)
        ];

        // Initialize cURL
        $curl = curl_init();

        if (!$curl) {
            throw new \RuntimeException('Failed to initialize cURL');
        }

        try {
            // Set basic cURL options
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->_url,
                CURLOPT_CONNECTTIMEOUT => $this->curl_timeout,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => false,
            ]);

            // SSL configuration
            if ($this->curl_ignoressl) {
                curl_setopt_array($curl, [
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
            }

            // Additional cURL options
            foreach ($this->curl_opts as $key => $value) {
                curl_setopt($curl, $key, $value);
            }

            // HTTP headers
            if ($this->curl_headers) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $this->curl_headers);
            }

            // Execute request
            $response = curl_exec($curl);
            $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $this->response = 'cURL error: ' . curl_error($curl);
            } else {
                $this->response = $response;

                // Detect JSON response
                $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
                $this->is_json = str_contains($content_type, 'application/json') ||
                               str_contains($content_type, 'application/javascript');
            }
        } finally {
            curl_close($curl);
        }
    }

    /**
     * Generate cryptographically secure random entropy
     *
     * @param int $length Length of the generated string
     * @return string Random hex string
     * @throws Exception
     */
    public static function generateEntropy(int $length = 64): string
    {
        $bytes_needed = intval(ceil($length / 2));
        $random_bytes = random_bytes($bytes_needed);
        return substr(bin2hex($random_bytes), 0, $length);
    }

    // =====================================
    // Server Methods
    // =====================================

    /**
     * Validate signature for received payload
     *
     * @param string $payload Received payload
     * @param string $signature Received signature (base64 encoded)
     * @return bool True if signature is valid
     * @throws Exception
     */
    public function validateSignature(string $payload, string $signature): bool
    {
        $this->validateServerConfig($payload, $signature);

        $signature_binary = base64_decode($signature, true);
        if ($signature_binary === false) {
            throw new \InvalidArgumentException('Invalid base64 signature');
        }

        return openssl_verify($payload, $signature_binary, $this->_pubkey, $this->_algo) === 1;
    }

    // =====================================
    // Private Validation Methods
    // =====================================

    /**
     * Validate private key format
     *
     * @param string $data Private key data
     * @return void
     * @throws InvalidArgumentException
     */
    private function validatePrivateKey(string $data): void
    {
        if (!str_starts_with($data, '-----BEGIN PRIVATE KEY-----')) {
            throw new \InvalidArgumentException('Invalid private key format');
        }

        $this->_privkey = $data;
    }

    /**
     * Validate public key format
     *
     * @param string $data Public key data
     * @return void
     * @throws InvalidArgumentException
     */
    private function validatePublicKey(string $data): void
    {
        if (!str_starts_with($data, '-----BEGIN PUBLIC KEY-----')) {
            throw new \InvalidArgumentException('Invalid public key format');
        }

        $this->_pubkey = $data;
    }

    /**
     * Validate and read key file
     *
     * @param string $filename Path to key file
     * @return string File contents
     * @throws Exception
     */
    private function validateKeyFile(string $filename): string
    {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException("Key file not found: {$filename}");
        }

        $filedata = @file_get_contents($filename);

        if ($filedata === false) {
            $error = error_get_last();
            $error_msg = $error['message'] ?? 'Unknown error';
            throw new \RuntimeException("Failed to read key file: {$error_msg}");
        }

        if (empty($filedata)) {
            throw new \InvalidArgumentException("Key file is empty: {$filename}");
        }

        return $filedata;
    }

    /**
     * Validate client configuration before sending request
     *
     * @param string $payload Payload to send
     * @return void
     * @throws RuntimeException
     */
    private function validateClientConfig(string $payload): void
    {
        if (empty($payload)) {
            throw new \InvalidArgumentException('Payload cannot be empty');
        }

        if (!$this->_url) {
            throw new \RuntimeException('URL not specified');
        }

        if (!$this->_privkey) {
            throw new \RuntimeException('Private key not specified');
        }
    }

    /**
     * Validate server configuration before verifying signature
     *
     * @param string $payload Received payload
     * @param string $signature Received signature
     * @return void
     * @throws RuntimeException
     */
    private function validateServerConfig(string $payload, string $signature): void
    {
        if (empty($payload)) {
            throw new \InvalidArgumentException('Payload cannot be empty');
        }

        if (empty($signature)) {
            throw new \InvalidArgumentException('Signature cannot be empty');
        }

        if (!$this->_pubkey) {
            throw new \RuntimeException('Public key not specified');
        }
    }
}
