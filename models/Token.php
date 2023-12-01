<?php
namespace models;

class Token
{        
    private $bearer_token = "xxxxxxxxxxx";
    
    /**
     * pegarTokenBearer
     * RETORNA O ATRIBUTO 'bearer_token'
     * @return string
     */
    public function pegarTokenBearer(): string
    {
        return $this->bearer_token;
    }

    /**
     * pegarAutorizacaoCabecalho
     * RETORNA O CABEÇALHO DA REQUISIÇÃO
     * @return string|null
     */
    private function pegarAutorizacaoCabecalho(): string|null
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    
    /**
     * verificarTokenBearer
     * VERIFICA TOKEN BEARER
     * @return bool
     */
    public function verificarTokenBearer(): bool
    {
        $headers = $this->pegarAutorizacaoCabecalho();

        if (empty($headers)) {
            return false;
            exit;
        }

        //MATCHES RECEBE O TOKEN BEAERER SE EXISTIR
        preg_match('/Bearer\s(\S+)/', $headers, $matches);

        if ($matches[1] != $this->bearer_token) {
            return false;
            exit;
        }

        return true;
    }
}