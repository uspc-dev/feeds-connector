<?php

namespace USPC\Feeds;

/**
* HTML Template for connecting remote feeds locally
*/
class FeedsConnector
{

    const SEARCH_BY_NAME = 'name';
    const SEARCH_BY_DOMAIN = 'domain';

    /**
     * Twig 
     *
     * @var Twig_Environment
     **/
    static private $twig = null;

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

    
    public function __construct(ServiceConnection $sc, StoreInterface $si)
    {
        $this->sc = $sc;
        $this->si = $si;

        $this->loadTwig();
    }

    /**
     * Load Twig Environment
     *
     * @return void
     * @author Mykola Martynov
     **/
    private function loadTwig()
    {
        $twig = self::$twig;
        if (!is_null($twig)) {
            return;
        }

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/FeedsTemplate');
        $twig = new \Twig_Environment($loader);

        self::$twig = $twig;
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
            return self::$twig->render('error.html.twig');
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

        return self::$twig->render('index.html.twig', ['store' => $store, 'merchants' => $merchants]);
        // return new FeedsTemplate\IndexTemplate($store, $merchants);
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
        $search_type = $this->getSearchType();

        return self::$twig->render('search-merchants.html.twig', [
            'store' => $store,
            'merchants' => [],
            'search_type' => $search_type,
            'msgMerchantsEmpty' => 'No merchants found.',
        ]);
    }

    /**
     * Display form to add new sources with search result.
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function searchByNameAction()
    {
        return $this->searchBy('name', $this->getSearchName());
    }

    /**
     * Display form to add new sources with search result.
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function searchByDomainAction()
    {
        return $this->searchBy('domain', $this->getDomainName());
    }

    /**
     * Return template for the search result form.
     *
     * @param  string  $method_type
     * @param  string  $data
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function searchBy($method_type, $data)
    {
        $store = $this->si;

        $search_type = $this->getSearchType();

        $merchants = $this->findMerchantsBy($method_type, $data);

        return self::$twig->render('search-merchants.html.twig', [
            'store' => $store,
            'merchants' => $merchants,
            'search_type' => $search_type,
            'msgMerchantsEmpty' => 'No merchants found.',
            'rowSelect' => true,
        ]);
    }

    /**
     * Return merchants founded by the specified search method.
     *
     * @param  string  $method_type
     * @param  string  $data
     * @return array
     * @author Mykola Martynov
     **/
    private function findMerchantsBy($method_type, $data)
    {
        $repository = new MerchantRepository();
        $repository->setConnection($this->sc);

        $method = 'findBy' . ucfirst(strtolower($method_type));
        $merchants = call_user_method($method, $repository, $data);

        # sort by name/network
        usort($merchants,
          function($a, $b) {
            $result = strcasecmp($a['name'], $b['name']);
            if (!$result) {
              $result = strcasecmp($a['network'], $b['network']);
            }
            return $result;
        });

        return $merchants;
    }

    /**
     * Return the name for merchant searched by the user.
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function getSearchName()
    {
        $name = empty($_POST['merchant_name']) ? '' : $_POST['merchant_name'];
        return $name;
    }

    /**
     * Return the domain for merchant searched by the user.
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function getDomainName()
    {
        $domain = empty($_POST['merchant_domain']) ? '' : $_POST['merchant_domain'];
        return $domain;
    }

    /**
     * Return type of the serach field
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function getSearchType($default = self::SEARCH_BY_NAME)
    {
        $type = empty($_POST['search_type']) ? $default : $_POST['search_type'];
        return $type;
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
        usort(
            $merchants,
            function ($a, $b) {
                $result = strcasecmp($a['name'], $b['name']);
                if (!$result) {
                    $result = strcasecmp($a['network'], $b['network']);
                }
                return $result;
            }
        );

        return $merchants;
    }
}
