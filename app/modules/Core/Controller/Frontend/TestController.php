<?php
namespace App\Module\Core\Controller\Frontend;

use App\Core\App;

class TestController extends BaseController {

    public function testAction()
    {
        App::get()->profiler->start("App::Core::IndexController::indexAction");
        App::get()->profiler->stop("App::Core::IndexController::indexAction");
        return $this->apiResponse(true,[
            "controller" => __CLASS__,
            "action" => __METHOD__
        ]);
    }

}