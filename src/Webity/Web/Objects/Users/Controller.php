<?php
namespace Webity\Web\Objects\Users;

use Webity\Web\Controller\Controller as WebityController;

/**
 * My custom controller.
 *
 * @since  1.0
 */
class Controller extends WebityController
{
    /**
     * Executes the controller.
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function doExecute()
    {
        echo time();
    }
}