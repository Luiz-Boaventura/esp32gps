<?php
namespace models;

class Login
{        
    private $usuario = "xxxxxxxxxxx";
    private $senha = "xxxxxxxxxxx";
        
    /**
     * validar
     * VALIDA O LOGIN ENVIADO PELO DO DISPOSITIVO ESP32
     * @param  string $usuario
     * @param  string $senha
     * @return bool
     */
    public function validar(string $usuario, string $senha): bool
    {
        if ($this->usuario != $usuario || $this->senha != $senha) {
            return false;
            exit;
        }

        return true;
    }
}