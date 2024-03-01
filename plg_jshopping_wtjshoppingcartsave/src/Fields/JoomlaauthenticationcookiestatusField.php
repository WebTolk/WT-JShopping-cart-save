<?php
/**
 * @package       WT JoomShopping content to Joomla Articles
 * @version       2.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (C) 2023 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

namespace Joomla\Plugin\Jshopping\Wtjshoppingcartsave\Fields;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\NoteField;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

class JoomlaauthenticationcookiestatusField extends NoteField
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
        return ' ';
    }

    /**
     * @return  string  The field label markup.
     *
     * @since   1.7.0
     */
    protected function getLabel(): string
    {
		if(!PluginHelper::isEnabled('authentication','cookie')){
			$message = '<p>Plugin Authentication - Cookie is not enabled</p>';
			return $this->renderMessage($message,'warning');
		}

		$message = '<p>Plugin <code>Authentication - Cookie</code> is enabled.</p>';

		if(!PluginHelper::isEnabled('system','remember')){
			$message .= '<p>Plugin System - Remember is not enabled</p>';
			return $this->renderMessage($message,'warning');
		}
		$auth_cookie_params = new Registry(PluginHelper::getPlugin('authentication','cookie')->params);
		$cookie_lifetime = $auth_cookie_params->get('cookie_lifetime','60');
		$message .= '<p>Cookie lifetime param is <span class="badge bg-success">'.$cookie_lifetime.'</span> days. User\'s cart will save for <span class="badge bg-success">'.$cookie_lifetime.'</span> days.</p>
		<hr>
		<p>Plugin <code>System - Remember</code> is enabled.</p>';
	    return $this->renderMessage($message,'success');

    }

    /**
     * Method to get the field title.
     *
     * @return  string  The field title.
     *
     * @since   1.7.0
     */
    protected function getTitle(): string
    {
        return $this->getLabel();
    }

	/**
	 * @param   string  $message
	 * @param   string  $type 'success', 'danger', 'info' etc. 'info' by default
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function renderMessage(string $message, string $type = 'info') : string
	{
		$class = 'alert-'.$type;

		return '</div>
		<div class="alert '.$class.'">
			'.$message.'
		</div><div>
		';
	}
}
?>