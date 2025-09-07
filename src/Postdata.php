<?php
namespace cCrud;

defined('CCRUD_PATH') OR define('CCRUD_PATH', str_replace('\\', '/', dirname(__file__)));

/**
 * Classe responsável por manipular dados enviados via POST.
 * Fornece métodos auxiliares para manipular, consultar e converter
 * os valores recebidos, mantendo a consistência do cCrud.
 */
class Postdata
{
    /**
     * Referência ao objeto principal do cCrud.
     *
     * @var cCrud|null
     */
    private ?cCrud $cCrud = null;

    /**
     * Dados recebidos via POST.
     *
     * @var array<string,mixed>
     */
    private array $postdata = [];

    /**
     * Inicializa a classe com os dados do formulário.
     *
     * @param array<string,mixed> $postdata Dados do formulário.
     * @param cCrud               $cCrud    Instância principal do cCrud.
     */
    public function __construct(array $postdata, cCrud $cCrud)
    {
        $this->cCrud = $cCrud;
        $this->postdata = $postdata;
    }

    /**
     * Define um valor para um campo de POST.
     *
     * Se o nome representar múltiplos campos, todos receberão o mesmo valor.
     *
     * @param string $name  Nome do campo.
     * @param mixed  $value Valor a ser atribuído.
     *
     * @return self
     */
    public function set(string $name, mixed $value): self
    {
        $fdata = $this->cCrud->_parse_field_names($name, 'Postdata');
        foreach ($fdata as $key => $_) {
            $this->postdata[$key] = $value;
        }
        $this->cCrud->unlock_field($name); // Garante que o campo possa ser reutilizado
        return $this;
    }

    /**
     * Remove um campo do conjunto de dados do POST.
     *
     * @param string $name Nome do campo a ser removido.
     *
     * @return self
     */
    public function del(string $name): self
    {
        $fdata = $this->cCrud->_parse_field_names($name, 'Postdata');
        foreach ($fdata as $key => $_) {
            unset($this->postdata[$key]);
        }
        return $this;
    }

    /**
     * Retorna o valor de um campo enviado.
     *
     * @param string $name Nome do campo.
     *
     * @return mixed|null Valor do campo ou null se não existir.
     */
    public function get(string $name): mixed
    {
        $fdata = $this->cCrud->_parse_field_names($name, 'Postdata');
        $fname = key($fdata);
        return $this->postdata[$fname] ?? null;
    }

    /**
     * Converte os dados armazenados em array.
     *
     * @return array<string,mixed> Dados do POST processados.
     */
    public function toArray(): array
    {
        return $this->postdata; // Entrega os dados para manipulação externa
    }
}

// Mantém compatibilidade com o nome legado.
class_alias(Postdata::class, __NAMESPACE__ . '\\cCrudPostdata');
