<?php

namespace test;

use USPC\Feeds\StoreInterface;

/**
 * Stub for the store info with implemented StoreInterface
 */
class StoreInfo implements StoreInterface {

    /**
     * Id's for the store feeds.
     *
     * @var array of int
     **/
    private $feeds = [19, 20, 21, 22, 23];

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
        return $this->feeds;
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
        foreach ($merchants as $merchant_id) {
            if (!in_array($merchant_id, $this->feeds)) {
                $this->feeds[] = $merchant_id;
            }
        }
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
        foreach ($merchants as $merchant_id) {
            $index = array_search($merchant_id, $this->feeds);
            if ($index !== false) {
                unset($this->feeds[$merchant_id]);
            }
        }
    }

}

