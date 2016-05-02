<?php

namespace test;

use USPC\Feeds\StoreInterface;

/**
 * Stub for the store info with implemented StoreInterface
 */
class StoreInfo implements StoreInterface {

    /**
     * Return name for the store
     *
     * @return string
     * @author Mykola Martynov
     **/
    public function getName()
    {
        return 'GoalieMonkey.com';
    }

    /**
     * Return list of id's for the associated remote merchants.
     *
     * @return array
     * @author Mykola Martynov
     **/
    public function getFeeds()
    {
        return [19, 20, 21, 22, 23];
    }

    /**
     * Add new remote merchants to the store
     *
     * @param  array  $merchants
     * @return void
     * @author Mykola Martynov
     **/
    public function addFeeds($merchants)
    {
        // !!! stub
    }

    /**
     * Remove merchants from the associated store list.
     *
     * @param  array  $merchants
     * @return void
     * @author Mykola Martynov
     **/
    public function removeFeeds($merchants)
    {
        // !!! stub
    }

}

