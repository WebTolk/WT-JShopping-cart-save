<?php
/**
 * @package    WT JShopping cart save
 * @version       1.1.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @сopyright  Copyright (c) 2024 Sergey Tolkachyov. All rights reserved.
 * @license       GNU/GPL3 http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

namespace Joomla\Plugin\Task\Wtjshoppingclearsavedcarts\Fields;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\NoteField;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

class WtjshoppingcartsavelifetimestatusField extends NoteField
{

    protected $type = 'Wtjshoppingcartsavelifetimestatus';

    /**
     * WT jshopping cart save plugin params
     *
     * @var mixed $wtjshoppingcartsave
     * @since 1.1.0
     */
    protected $wtjshoppingcartsave = false;

    public function __construct($form = null)
    {
        parent::__construct($form);

        if(PluginHelper::isEnabled('jshopping','wtjshoppingcartsave'))
        {
            $plugin = PluginHelper::getPlugin('jshopping','wtjshoppingcartsave');

            $this->wtjshoppingcartsave = new Registry($plugin->params);
        }


    }

    /**
     * Method to get the field input markup for a spacer.
     * The spacer does not have accept input.
     *
     * @return  string  The field input markup.
     *
     * @since   1.7.0
     */
    protected function getInput(): string
    {

        if(!$this->wtjshoppingcartsave)
        {
            return Text::_('PLG_WTJSHOPPINGCLEARSAVEDCARTS_WT_JSHOPPING_CART_SAVE_LIFETIME_STATUS_FIELD_PLUGIN_DISABLED');
        }

        $message = '';
        $life_time = (int)$this->wtjshoppingcartsave->get('cookie_lifetime', 365);
        if($this->wtjshoppingcartsave->get('use_joomla_auth_cookie_plugin_lifetime_setting', 0) == 1)
        {
            $message = Text::sprintf('PLG_WTJSHOPPINGCLEARSAVEDCARTS_WT_JSHOPPING_CART_SAVE_LIFETIME_STATUS_FIELD_PLUGIN_USES_JOOMLA_AUTH_COOKIE_PLUGIN_PARAMS', $life_time, $life_time);
        } else {
            $message = Text::sprintf('PLG_WTJSHOPPINGCLEARSAVEDCARTS_WT_JSHOPPING_CART_SAVE_LIFETIME_STATUS_FIELD_WT_JSHOPPING_CART_SAVE_LIFE_TIME', $life_time);
        }

        return $message;
    }

}
