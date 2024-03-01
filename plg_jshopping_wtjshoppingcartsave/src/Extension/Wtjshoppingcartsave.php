<?php

namespace Joomla\Plugin\Jshopping\Wtjshoppingcartsave\Extension;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

class Wtjshoppingcartsave extends CMSPlugin implements SubscriberInterface
{
	protected $autoloadlanguage = true;

	protected $allowLegacyListeners = false;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onConstructJshopTempCart' => 'onConstructJshopTempCart',
			'onAfterSaveToSessionCart' => 'onAfterSaveToSessionCart',
			'onBeforeEditUsers'        => 'onBeforeEditUsers',
			'onBeforeDisplayUsers'     => 'onBeforeDisplayUsers',
		];
	}

	public function onConstructJshopTempCart($event): void
	{
		$tempCart                                = $event->getArgument(0);
		$tempCart->load_product_temp_cart_type[] = 'cart';
		$savedays                                = $this->params->get('cookie_lifetime', 365);

		if ($this->params->get('use_joomla_auth_cookie_plugin_lifetime_setting', 0) == 1
			&& PluginHelper::isEnabled('authentication', 'cookie'))
		{
			$auth_cookie_params = new Registry(PluginHelper::getPlugin('authentication', 'cookie')->params);
			$savedays           = $auth_cookie_params->get('cookie_lifetime', '60');
		}

		$tempCart->savedays = $savedays;
		$event->setArgument(0, $tempCart);
	}

	public function onAfterSaveToSessionCart($event): void
	{
		$cart = $event->getArgument(0);

		$tempcart    = \JSFactory::getModel('Tempcart', 'Site');
		$tempcart_id = (string) $tempcart->getIdTempCart();
		$this->deleteTempCartDate($tempcart_id);

		if (count($cart->products) == 0)
		{
			return;
		}
		$this->insertTempCartDate($tempcart_id);
	}

	private function deleteTempCartDate(string $temp_cart_id): bool
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__plg_jshopping_wtjshoppingcartsave'))
			->where($db->quoteName('temp_cart_id') . ' = :cartid')
			->bind(':cartid', $temp_cart_id, ParameterType::STRING);

		return $db->setQuery($query)->execute();
	}

	private function insertTempCartDate(string $temp_cart_id): bool
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$tz            = new \DateTimeZone(Factory::getApplication()->get('offset'));
		$date_modified = (new Date('now'))->setTimezone($tz);
		$date_modified = $date_modified->toSql(true);

		$query   = $db->getQuery(true);
		$columns = ['temp_cart_id', 'date_modified'];
		$values  = [
			$db->quote($temp_cart_id),
			$db->quote($date_modified)
		];
		$query->insert($db->quoteName('#__plg_jshopping_wtjshoppingcartsave'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));

		return $db->setQuery($query)->execute();
	}

	private function getTempCartDate(string $temp_cart_id): string
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = $db->getQuery(true)
			->select($db->quoteName('date_modified'))
			->from($db->quoteName('#__plg_jshopping_wtjshoppingcartsave'))
			->where($db->quoteName('temp_cart_id') . ' = :cartid')
			->bind(':cartid', $temp_cart_id, ParameterType::STRING);

		return $db->setQuery($query)->loadResult();
	}

	public function onBeforeEditUsers($event): void
	{
		$view = $event->getArgument(0);
		if (property_exists($view, 'etemplatevarend'))
		{
			$jshopConfig = \JSFactory::getConfig();
			// Get the path for the layout file
			$path    = PluginHelper::getLayoutPath('jshopping', 'wtjshoppingcartsave');
			$user_id = $view->user->user_id;

			$tempcart = \JSFactory::getModel('Tempcart', 'Site');

			$products_saved_cart = $tempcart->getListRowsByUserId($user_id, 'cart');

			/** @var  $products_saved_cart_date_modified string Last date and time when user has modify his cart */
			$products_saved_cart_date_modified = '';
			$id_cookie = $products_saved_cart[0]->id_cookie;
			if (!empty($products_saved_cart))
			{
				$products_saved_cart = $tempcart->getProducts('cart');
				$products_saved_cart_date_modified = $this->getTempCartDate($id_cookie);
			}
			$products_wishlist = [];
			if ($jshopConfig->enable_wishlist)
			{
				$products_wishlist = $tempcart->getListRowsByUserId($user_id, 'wishlist');

				if (!empty($products_wishlist))
				{
					$products_wishlist = $tempcart->getProducts('wishlist');
				}
			}

			// Render the pagenav
			ob_start();
			include $path;
			$html = ob_get_clean();

			if (!empty($view->etemplatevarend))
			{
				$view->etemplatevarend .= $html;
			}
			else
			{
				$view->etemplatevarend = $html;
			}

		}
		$event->setArgument(0, $view);
	}

	public function onBeforeDisplayUsers($event): void
	{
		$view = $event->getArgument(0);
		$view->tmp_html_col_after_email = '<th><i class="icon-cart"/> / <i class="icon-heart"/></th>';

		$tempcart = \JSFactory::getModel('Tempcart', 'Site');


		foreach ($view->rows as &$row){
			$lost_cart_badges = '';
			if(property_exists($row,'user_id') && !empty($row->user_id)){

				$products_cart = $tempcart->getListRowsByUserId($row->user_id, 'cart');

				if (!empty($products_cart))
				{
					$products_count = count($tempcart->getProducts('cart'));
					$lost_cart_badges .= '<span class="badge bg-danger"><i class="icon-cart" ></i> '.$products_count.'</span>';
				}

				$products_wishlist = $tempcart->getListRowsByUserId($row->user_id, 'wishlist');

				if (!empty($products_wishlist))
				{
					$products_count = count($tempcart->getProducts('wishlist'));
					$lost_cart_badges .= '<span class="badge bg-info"><i class="icon-heart"></i> '.$products_count.'</span>';
				}


			}
			//u_name
//			$products_saved_cart = $tempcart->getListRowsByUserId($user_id, 'cart');
			if(property_exists($row,'tmp_html_col_after_email'))
			{
				if(!empty($row->tmp_html_col_after_email))
				{
					$row->tmp_html_col_after_email .= '<td>'.$lost_cart_badges.'</td>';
				} else {
					$row->tmp_html_col_after_email = '<td>'.$lost_cart_badges.'</td>';
				}
			}
		}

		$event->setArgument(0, $view);
	}

	// tmp_html_col_after_email

}