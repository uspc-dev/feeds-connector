<?php

namespace USPC\Feeds\FeedsTemplate;

use USPC\Feeds\StoreInterface;

class IndexTemplate extends BaseTemplate {

    /**
     * Store information
     *
     * @var StoreInterface
     **/
    private $store;

    /**
     * Available merchants
     *
     * @var array
     **/
    private $merchants;

    /**
     * save store and merchants for later use
     * 
     * @param StoreInterface $store
     * @param array          $merchants
     */
    function __construct(StoreInterface $store, $merchants) {
        $this->store = $store;
        $this->merchants = $merchants;
    }


    /**
     * Return HTML template (partial html) for the list of merchants.
     *
     * @return string
     * @author Mykola Martynov
     **/
    public function render()
    {
        return '<div class="container">'
            . $this->blockHeader()
            . $this->blockContent()
            . $this->blockFooter()
            . '</div>';
    }

    /**
     * Header for merchants list
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function blockHeader()
    {
        return <<< BLOCK_HEADER
<div class="panel panel-primary">
    <div class="panel-heading">List of merchants whose coupons are used</div>
BLOCK_HEADER;
    }

    /**
     * Footer for merchants list
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function blockFooter()
    {
        return <<< BLOCK_FOOTER
  <div class="panel-footer">
    <form method="post">
      <button type="submit" name="action" value="new-source" class="btn btn-primary">Add New Source</button>
      <button type="submit" name="action" value="fetch-coupons" class="btn btn-default">Fetch New Coupons</button>
    </form>
  </div>
</div>
BLOCK_FOOTER;
    }

    /**
     * List of merchants
     *
     * @return string
     * @author Mykola Martynov
     **/
    private function blockContent()
    {
        $merchants = $this->merchants;

        # display default message if no merchants.
        if (empty($merchants)) {
            return <<< BLOCK_CONTENT_EMPTY
  <div class="panel-body">No merchants.</div>
BLOCK_CONTENT_EMPTY;
        }

        $tbody = '';
        $index = 0;

        # create rows for each mearchant
        foreach ($merchants as $merchant) {
            $index++;
            $tbody .= <<< TABLE_ROWS
        <tr role="checkbox-row-select">
          <td>$index</td>
          <td>$merchant[name]</td>
          <td>$merchant[coupons]</td>
          <td>$merchant[network]</td>
          <td>$merchant[domain]</td>
        </tr>
TABLE_ROWS;
        }

        # create table
        return <<< BLOCK_CONTENT
  <table class="table table-hover">
    <thead>
      <tr class="bg-info">
        <th>#</th>
        <th>Merchant</th>
        <th>Coupons</th>
        <th>Network</th>
        <th>Domain</th>
      </tr>
    </thead>
    <tbody>
        $tbody
    </tbody>
  </table>
BLOCK_CONTENT;
    }

}