<?php

namespace USPC\Feeds;

interface StoreInterface {

    public function getName();

    public function getFeeds();

    public function addFeeds($merchants);

    public function removeFeeds($merchants);
    
}