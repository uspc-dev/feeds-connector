<?php

namespace USPC\Feeds;

/**
* HTML Template for connecting remote feeds locally
*/
class FeedsConnector
{

    /**
     * ServiceConnection instance
     *
     * @var ServiceConnection
     **/
    private $sc;

    /**
     * Store Interface to get/set information about feeds
     *
     * @var StoreInterface
     **/
    private $store;

    
    function __construct(ServiceConnection $sc, StoreInterface $si)
    {
        $this->sc = $sc;
        $this->si = $si;
    }

    /**
     * Processing all actions and return template
     *
     * @return HtmlTemplate
     * @author Mykola Martynov
     **/
    public function process()
    {
        // !!! stub
        return new FeedsTemplate\EmptyTemplate();
    }

    /**
     * Return information about merchants by it's IDs
     *
     * @param  array  $ids  List of feed merchant IDs
     * @return array
     * @author Mykola Martynov
     **/
    private function merchantsInfo($ids)
    {
        // get merchants repository
        $repo = new MerchantRepository();
        $repo->setConnection($this->sc);

        // get all available merchants
        $merchants = $repo->find($ids);

        // sort by name & network
        usort($merchants, function($a, $b) {
            $result = strcasecmp($a['name'], $b['name']);
            if (!$result) {
              $result = strcasecmp($a['network'], $b['network']);
            }
            return $result;
        });

        return $merchants;
    }

}