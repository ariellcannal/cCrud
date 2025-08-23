<?php
namespace cCrud\Controller;

use App\Controllers\BaseController;
use cCrud\Libraries\cCrud;
use CodeIgniter\Model;

class cCrudController extends BaseController
{
    /**
     * Model utilizado pelo controlador.
     */
    protected Model $model;

    public function ajax(Model $model)
    {
        return cCrud::get_requested_instance($model);
    }

    protected function obterInstancia(Model $model, $name = false): cCrud
    {
        $this->model = $model;
        return cCrud::get_instance($model, $name);
    }

    protected function armazenarSessao()
    {
        $_SESSION['xcrud_sess'] = cCrud::export_session();
    }

    protected function restaurarSessao()
    {
        cCrud::import_session($_SESSION['xcrud_sess']);
    }
}