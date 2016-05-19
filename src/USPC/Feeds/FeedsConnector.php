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
        # get list of already existing merchants
        $merchants = $this->merchantsInfo($this->si->getFeeds());

        return self::$twig->render('index.html.twig', ['merchants' => $merchants]);
    }

    /**
     * Add selected merchantes to the store and display index page.
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function addMerchantsAction()
    {
        $merchants = $this->getMerchantsId();
        $this->si->addFeeds($merchants);

        return $this->indexAction();
    }

    /**
     * Remove selected merchants from the store after confirmation and display index page.
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function removeMerchantsAction()
    {
        $merchants_id = $this->getMerchantsId();
        $confirmed = $this->getConfirmStatus();

        # remove feeds
        if (!empty($merchants_id) && $confirmed) {
            $this->si->removeFeeds($merchants_id);
        }

        # display confirmation page
        elseif (!empty($merchants_id)) {
            return $this->confirmRemoveAction();
        }

        return $this->indexAction();
    }

    /**
     * Display confirmation page for deleting selected merchants
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function confirmRemoveAction()
    {
        $merchants = $merchants = $this->merchantsInfo($this->getMerchantsId());
        return self::$twig->render('confirm-remove.html.twig', [
            'merchants' => $merchants,
        ]);
    }

    /**
     * Display form to search/add new merchants
     *
     * @return BaseTemplate
     * @author Mykola Martynov
     **/
    private function newSourceAction()
    {
        $search_type = $this->getSearchType();

        return self::$twig->render('search-merchants.html.twig', [
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
        $search_type = $this->getSearchType();
        $merchants = $this->findMerchantsBy($method_type, $data);

        return self::$twig->render('search-merchants.html.twig', [
            'merchants' => $merchants,
            'search_type' => $search_type,
            'msgMerchantsEmpty' => 'No merchants found.',
            'rowSelect' => true,
            'search_text' => $data,
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
     * Return list of merchants id, selected by the user in search result form.
     *
     * @return array
     * @author Mykola Martynov
     **/
    private function getMerchantsId()
    {
        $merchants_id = empty($_POST['merchants_id']) ? [] : $_POST['merchants_id'];
        return !is_array($merchants_id) ? [] : $merchants_id;
    }

    /**
     * Return confirm status.
     *
     * @return boolean
     * @author Mykola Martynov
     **/
    private function getConfirmStatus()
    {
        return !empty($_POST['confirmed']);
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
