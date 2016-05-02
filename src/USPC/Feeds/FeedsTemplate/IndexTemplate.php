<?php

namespace USPC\Feeds\FeedsTemplate;

use USPC\Feeds\StoreInterface;

class IndexTemplate extends BaseListTemplate {

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
        $list = $this->makeList($merchants);

        return '<div class="panel-body">' . $list . '</div>';
    }

}