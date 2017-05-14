<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\ajaxregistrationcheck\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @version 1.0.0 */
class listener implements EventSubscriberInterface
{
    /** @var config $config Configuration object */
    protected $config;

    /** @var helper $helper Controller helper object */
    protected $helper;

    /** @var template $template Template object */
    protected $template;

    /** @var user $user User object */
    protected $user;

    /**
     * Listener constructor
     *
     * @access public
     * @since  1.0.0
     *
     * @param config   $config   Configuration object
     * @param helper   $helper   Controller helper object
     * @param template $template Template object
     * @param user     $user     User object
     */
    public function __construct(config $config, helper $helper, template $template, user $user)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
    }

    /**
     * Function that returns the subscribed events
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Array with the subscribed events
     */
    static public function getSubscribedEvents()
    {
        return array(
            'core.ucp_register_data_before' => 'assign_register_data',
        );
    }

    /**
     * Function to assign all needed data to the registration form
     *
     * @access public
     * @since  1.0.0
     */
    public function assign_register_data()
    {
        $username_rule = $this->config['allow_name_chars'];
        switch ($username_rule)
        {
            case 'USERNAME_CHARS_ANY':
                $username_rule = '^.*$';
            break;
        }
        $this->template->assign_vars(array(
            'PCGF_AJAXREGISTRATIONCHECK_USERNAME_MIN'                => $this->config['min_name_chars'],
            'PCGF_AJAXREGISTRATIONCHECK_USERNAME_MAX'                => $this->config['max_name_chars'],
            'PCGF_AJAXREGISTRATIONCHECK_USERNAME_RULE'               => $username_rule,
            'PCGF_AJAXREGISTRATIONCHECK_USERNAME_INVALID_BOUNDARIES' => $this->user->lang($this->config['allow_name_chars'] . '_EXPLAIN', $this->config['min_name_chars'], $this->config['max_name_chars']),
            'PCGF_AJAXREGISTRATIONCHECK_CHECK_USERNAME_LINK'         => $this->helper->route('pcgf_ajaxregistrationcheck_controller', array('type' => 'username')),
        ));
    }
}
