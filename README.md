# PHP Database Access Library


Esta é uma biblioteca PHP simples para facilitar a execução de consultas SQL em bancos de dados MySQL. Ela pode ser útil para você se estiver procurando uma maneira fácil de se conectar a um banco de dados e executar consultas sem precisar escrever código repetitivo.

## Como usar

---

Para usar a biblioteca, primeiro você precisará incluir o arquivo database.php no seu projeto e instanciar a classe Database, passando as configurações do banco de dados como parâmetros. Por exemplo:

```php
Use libDb\Database;
require_once 'database.php';

$cfg_options = array(
    'host' => 'localhost',
    'database' => 'meu_banco',
    'username' => 'usuario',
    'password' => 'senha'
);

$db = new libDb\Database($cfg_options);
```

Uma vez instanciada a classe, você pode usar os métodos **execute_query** e **execute_non_query** para executar consultas SQL.

## Método execute_query

---

O método execute_query é usado para executar consultas SQL que retornam resultados. Ele retorna um objeto padrão contendo informações sobre o resultado da consulta, incluindo os resultados em si, o número de linhas afetadas e o último ID inserido (se houver). Exemplo:

```php

$sql = 'SELECT * FROM minha_tabela WHERE id = ?';
$parameters = array(1);

$result = $db->execute_query($sql, $parameters);

if ($result->status == 'success') {
// faça algo com os resultados
} else {
// lide com o erro
}
```

## Método execute_non_query

---

O método execute_non_query é usado para executar consultas SQL que não retornam resultados, como inserções, atualizações ou exclusões de dados. Ele retorna um objeto padrão contendo informações sobre o resultado da consulta, incluindo o número de linhas afetadas e o último ID inserido (se houver). Exemplo:

```php

$sql = 'INSERT INTO minha_tabela (campo1, campo2, campo3) VALUES (?, ?, ?)';
$parameters = array('valor1', 'valor2', 'valor3');

$result = $db->execute_non_query($sql, $parameters);

if ($result->status == 'success') {
// faça algo com o último ID inserido
} else {
// lide com o erro
}
```

## Configurações adicionais

---

Você pode configurar o tipo de retorno padrão passando um segundo parâmetro para o construtor da classe. O valor padrão é 'object', que retorna os resultados como objetos. Se você quiser que os resultados sejam retornados como arrays associativos em vez de objetos, basta passar 'array' como segundo parâmetro. Exemplo:

```php

$db = new libDb\Database($cfg_options, 'array');
```

## Considerações finais

Esta biblioteca é fornecida sem garantias, expressas ou implícitas. Use-a por sua conta e risco. Se você encontrar algum bug ou tiver alguma sugestão de melhoria, sinta-se à vontade para abrir uma issue no repositório do projeto no GitHub.
