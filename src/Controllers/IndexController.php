<?php

namespace MyFonero\Controllers;

class IndexController extends BaseController {

    protected $renderer;

    public function __construct($c) {
        parent::__construct($c);

        $this->renderer = $this->container->get('renderer');
    }

    public function index($request, $response, $args) {
        return $this->renderer->render($response, 'index.php', $args);
    }

}
