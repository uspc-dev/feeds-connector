<?php

namespace USPC\Feeds\FeedsTemplate;

class NoActionTemplate extends BaseTemplate {

    /**
     * Return bad action message
     *
     * @return string
     * @author Mykola Martynov
     **/
    public function render()
    {
        return '<div class="container"><section class="error"><h1>Error</h1><p>The page you requested is not exists.</p></section></div>';
    }

}