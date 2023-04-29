<?php

namespace libDb

use PDO;
use PDOException;
use stdClass;

// ============================================================================
class Database
{
    // propriedades
    private $_host;
    private $_database;
    private $_username;
    private $_password;
    private $_return_type;

    // ========================================================================
    public function __construct($cfg_options, $return_type = 'object')
    {
        // define as configurações de conexão
        $this->_host = $cfg_options['host'];
        $this->_database = $cfg_options['database'];
        $this->_username = $cfg_options['username'];
        $this->_password = $cfg_options['password'];

        // define o tipo de retorno padrão
        if (!empty($return_type) && $return_type == 'object') {
            $this->_return_type = PDO::FETCH_OBJ;
        } else {
            $this->_return_type = PDO::FETCH_ASSOC;
        }
    }

    // ========================================================================
    public function execute_query($sql, $parameters = null)
    {
        // executa uma consulta com retorno de resultados

        // conexão
        $connection = new PDO(
            'mysql:host=' . $this->_host . ';dbname=' . $this->_database . ';charset=utf8',
            $this->_username,
            $this->_password,
            array(PDO::ATTR_PERSISTENT => true)
        );

        $results = null;

        // prepara e executa a consulta
        try {

            $db = $connection->prepare($sql);
            if (!empty($parameters)) {
                $db->execute($parameters);
            } else {
                $db->execute();
            }
            $results = $db->fetchAll($this->_return_type);
        } catch (PDOException $err) {

            // fecha a conexão
            $connection = null;

            // retorna o erro
            return $this->_result('error', $err->getMessage(), $sql, null, 0, null);
        }

        // fecha a conexão
        $connection = null;

        // retorna o resultado
        return $this->_result('success', 'success', $sql, $results, $db->rowCount(), null);
    }

    // ========================================================================
    public function execute_non_query($sql, $parameters = null)
    {
        // executa uma consulta que não retorna resultados

        // conexão
        $connection = new PDO(
            'mysql:host=' . $this->_host . ';dbname=' . $this->_database . ';charset=utf8',
            $this->_username,
            $this->_password,
            array(PDO::ATTR_PERSISTENT => true)
        );

        // inicia a transação
        $connection->beginTransaction();

        // prepara e executa a consulta
        try {

            $db = $connection->prepare($sql);
            if (!empty($parameters)) {
                $db->execute($parameters);
            } else {
                $db->execute();
            }

            // último ID inserido
            $last_inserted_id = $connection->lastInsertId();

            // finaliza a transação
            $connection->commit();
        } catch (PDOException $err) {

            // desfaz todas as operações SQL em caso de erro
            $connection->rollBack();

            // fecha a conexão
            $connection = null;

            // retorna o erro
            return $this->_result('error', $err->getMessage(), $sql, null, 0, null);
        }

        $connection = null;

        // return result
        return $this->_result('success', 'success', $sql, null, $db->rowCount(), $last_inserted_id);
    }

    // Função privada que cria um objeto padrão para representar o resultado de uma consulta ao banco de dados.
    /**
     * @param string $status status da operação (success ou error)
     * @param string $message mensagem indicando se a operação foi bem sucedida ou a descrição do erro
     * @param string $sql a consulta SQL executada
     * @param mixed $results os resultados da consulta (pode ser nulo)
     * @param int $affected_rows número de linhas afetadas pela consulta (0 se a consulta não é do tipo UPDATE, DELETE ou INSERT)
     * @param mixed $last_id o último ID inserido (nulo se a consulta não é do tipo INSERT)
     * @return object objeto padrão contendo as informações do resultado da consulta
     */
    private function _result($status, $message, $sql, $results, $affected_rows, $last_id)
    {
        $tmp = new stdClass();
        $tmp->status = $status;
        $tmp->message = $message;
        $tmp->query = $sql;
        $tmp->results = $results;
        $tmp->affected_rows = $affected_rows;
        $tmp->last_id = $last_id;
        return $tmp;
    }
}