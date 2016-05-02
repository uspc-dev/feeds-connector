<?php

namespace USPC\Feeds\FeedsTemplate;

use USPC\Feeds\StoreInterface;

abstract class BaseListTemplate extends BaseTemplate {

    /**
     * Store information
     *
     * @var StoreInterface
     **/
    protected $store;

    /**
     * Available merchants
     *
     * @var array
     **/
    protected $merchants;

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

    
    protected function makeList($merchants, $with_select = false)
    {
        # display default message if no merchants.
        if (empty($merchants)) {
            return 'No merchants.';
        }

        $tbody = '';
        $th_select = empty($with_select) ? '' : '<th><input type="checkbox" role="checkbox-rows"></th>';
        $td_select = '';

        # create rows for each mearchant
        $index = 0;
        foreach ($merchants as $merchant) {
            $index++;

            if ($with_select) {
                $td_select = '<td><input type="checkbox" role="checkbox-one-row" name="merchants_id[]" value="'
                    . htmlspecialchars($merchant['id'], ENT_QUOTES)
                    . '"></td>';
            }

            $tbody .= <<< TABLE_ROWS
        <tr role="checkbox-row-select">
          $td_select
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
        $th_select
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