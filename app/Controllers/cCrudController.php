<?php
namespace cCrud\Controller;

use App\Controllers\BaseController;
use cCrud\Libraries\cCrud;

class cCrudController extends BaseController
{

    public function ajax()
    {
        return cCrud::get_requested_instance();
    }

    protected function obterInstancia($name = false): cCrud
    {
        return cCrud::get_instance($name);
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