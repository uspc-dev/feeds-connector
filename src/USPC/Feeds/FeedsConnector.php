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
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    public function process()
    {
        # check the current action
        $method = $this->getActionMethod();

        if (!method_exists($this, $method)) {
            return new FeedsTemplate\NoActionTemplate();
        }

        return $this->$method();
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Mykola Martynov
     **/
    private function getActionMethod()
    {
        $action = empty($_POST['action']) ? 'index' : $_POST['action'];

        # replace non alphabet characters to spaces and uppercase each word
        $action = trim(preg_replace('#[^a-z\s]+#', ' ', strtolower($action))) . ' action';

        # remove spaces and capitalize words
        $words = explode(' ', $action);
        $first_word = array_shift($words);
        $words = array_map('ucfirst', $words);

        $method = $first_word . join($words);

        return $method;
    }


    /**
     * Display list of already associated merchants and links to add new one.
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function indexAction()
    {
        $store = $this->si;

        # get list of already existing merchants
        $merchants = $this->merchantsInfo($store->getFeeds());

        return new FeedsTemplate\IndexTemplate($store, $merchants);
    }

    /**
     * Display form to search/add new merchants
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function newSourceAction()
    {
        $store = $this->si;

        # get list of merchants serached by name/domain
        // stub
        $merchants = [];

        return new FeedsTemplate\SearchTemplate($store, $merchants);
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