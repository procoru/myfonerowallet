<?php

namespace MyFonero\Controllers;

class BaseController {

    protected $container;

    public function __construct($c) {
        $this->container = $c;
    }

}

