<?php
namespace Webity\Web;

use Joomla\Controller\AbstractController;

/**
 * My custom controller.
 *
 * @since  1.0
 */
class Controller extends AbstractController
{
    /**
     * Executes the controller.
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function execute()
    {
        $displayData = array(
            'message' => $message
        );
        $layout = new \Webity\Web\Layout\File('Error');
        echo $layout->render($displayData);
    }
}