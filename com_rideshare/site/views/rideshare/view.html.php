<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class RideshareViewRideshare extends JView
{
    // Overwriting JView display method
    function display($tpl = null) 
    {
        // Assign data to the view (old code)
        //$this->msg = 'Hello World something';
        $this->msg = $this->get('Msg');
 
        // Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
 
        // Display the view
        parent::display($tpl);
    }
}
