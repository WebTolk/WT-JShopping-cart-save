<?php
/**
 * @package       WT JShopping cart save
 * @version       1.0.1
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @сopyright (c) 2024 - March 2024 Sergey Tolkachyov. All rights reserved.
 * @license       GNU/GPL3 http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

namespace Joomla\Plugin\Jshopping\Wtjshoppingcartsave\Fields;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\NoteField;
use Joomla\CMS\Form\FormField;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

class JoomlaauthenticationcookiestatusField extends FormField
{

    protected $type = 'Joomlaauthenticationcookiestatus';

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
        Factory::getApplication()
               ->getLanguage()
               ->load('plg_jshopping_wtjshoppingcartsave', JPATH_ADMINISTRATOR,null, true);

        $auth_cookie_plugin_state = false;
        if (PluginHelper::isEnabled('authentication', 'cookie'))
        {
            $auth_cookie_plugin_state = true;
        }
        $message = Text::sprintf(
            'PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_FIELD_AUTHENTICATION_COOKIE',
            ($auth_cookie_plugin_state ? Text::_('PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_ENABLED') : Text::_(
                'PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_DISABLED'
            ))
        );

        if ($auth_cookie_plugin_state)
        {
            $auth_cookie_params = new Registry(PluginHelper::getPlugin('authentication', 'cookie')->params);
            $cookie_lifetime    = $auth_cookie_params->get('cookie_lifetime', 60);
            $message            .= Text::sprintf(
                'PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_FIELD_SYSTEM_REMEMBER_COOKIE_LIFETIME',
                $cookie_lifetime,
                $cookie_lifetime
            );
        }


        $system_remember_plugin_state = false;
        if (PluginHelper::isEnabled('system', 'remember'))
        {
            $system_remember_plugin_state = true;
        }

        $message .= Text::sprintf(
            'PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_FIELD_SYSTEM_REMEMBER',
            ($system_remember_plugin_state ? Text::_('PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_ENABLED') : Text::_(
                'PLG_WTJSHOPPINGCARTSAVE_JOOMLA_PLUGINS_STATUS_DISABLED'
            ))
        );

        return $message;
    }
}
