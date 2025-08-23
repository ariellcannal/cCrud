<?php
namespace cCrud\Model;

use CodeIgniter\Model;

class cCrudModel extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    function consulta($query)
    {
        $r = $this->db->query($query, false);
        if (! $r) {
            return $this->db->error();
        }
        return $r;
    }

    function linhasAfetadas()
    {
        return $this->db->affected_rows();
    }

    function idInserido()
    {
        return $this->db->insert_id();
    }

    function resultado($r)
    {
        return $r->result_array();
    }

    function linha($r)
    {
        return $r->row_array();
    }

    function escaparString($value)
    {
        return $this->db->escape_str($value);
    }
}
