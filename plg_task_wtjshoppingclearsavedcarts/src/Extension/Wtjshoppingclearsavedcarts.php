<?php

/**
 * @package    WT JShopping cart save
 * @subpackage      Task.deleteactionlogs
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Task\Wtjshoppingclearsavedcarts\Extension;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * A task plugin. For Delete Action Logs after x days
 * {@see ExecuteTaskEvent}.
 *
 * @since 5.0.0
 */
final class Wtjshoppingclearsavedcarts extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    use TaskPluginTrait;

    /**
     * @var string[]
     * @since 5.0.0
     */
    private const TASKS_MAP = [
        'plg_task_wtjshoppingclearsavedcarts' => [
            'langConstPrefix' => 'PLG_TASK_WTJSHOPPINGCLEARSAVEDCARTS',
            'method'          => 'clearCarts',
            'form'            => 'wtjshoppingclearsavedcarts',
        ],
    ];

    /**
     * @var boolean
     * @since 5.0.0
     */
    protected $autoloadLanguage = true;


    /**
     * @inheritDoc
     *
     * @return string[]
     *
     * @since 5.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }


    /**
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @return integer  The routine exit code.
     *
     * @throws \Exception
     * @since  5.0.0
     */
    private function clearCarts(ExecuteTaskEvent $event): int
    {
        /** @var Registry $params Current task params */
        $params = new Registry($event->getArgument('params'));
        /** @var int $task_id The task id */
        $task_id = $event->getTaskId();

        $lifetime_setting_source = $params->get('lifetime_setting', 'wtjshoppingcartsave');
        $life_time               = 365;
        switch ($lifetime_setting_source)
        {
            case 'joomlaauthenticationcookie':
                if (PluginHelper::isEnabled('authentication', 'cookie'))
                {
                    $auth_cookie_params = new Registry(PluginHelper::getPlugin('authentication', 'cookie')->params);
                    $life_time          = $auth_cookie_params->get('cookie_lifetime', 60);
                }

                break;
            case 'wtjshoppingcartsave':
                if (PluginHelper::isEnabled('jshopping', 'wtjshoppingcartsave'))
                {
                    $wtjshoppingcartsave_params = new Registry(
                        PluginHelper::getPlugin('jshopping', 'wtjshoppingcartsave')->params
                    );
                    $life_time                  = (int) $wtjshoppingcartsave_params->get('cookie_lifetime', 365);
                }

                break;
            case 'custom':
                $life_time = (int) $params->get('cookie_lifetime', 365);
                break;

            default:
                $life_time = 365;
                break;
        }

        if (intval($life_time) == 0)
        {
            $this->logTask('Life time is sets to 0. Check it in task params.');

            return Status::KNOCKOUT;
        }
        $carts_count = $this->countSavedCarts();
        $db = $this->getDatabase();

        $delete_registered_users_cart = $params->get('delete_registered_users_cart', false);

        $delete_registered_users_cart_condition = ' AND '.$db->quoteName('jshop_cart.user_id') .' = 0';
        if($delete_registered_users_cart)
        {
            $delete_registered_users_cart_condition = '';
        }
        $query = 'DELETE ' . $db->quoteName('wt_cart') . ', ' . $db->quoteName(
                'jshop_cart'
            ) . ' FROM ' . $db->quoteName(
                '#__plg_jshopping_wtjshoppingcartsave',
                'wt_cart'
            ) . ' INNER JOIN ' . $db->quoteName('#__jshopping_cart_temp', 'jshop_cart') . ' ON ' . $db->quoteName(
                'wt_cart.temp_cart_id'
            ) . ' = ' . $db->quoteName('jshop_cart.id_cookie') . ' WHERE ' . $db->quoteName(
                'wt_cart.date_modified'
            ) . ' < (DATE(NOW() - INTERVAL ' . $life_time . ' DAY))'.$delete_registered_users_cart_condition.';';

        $result = false;
        try
        {
            $result = $db->setQuery($query)->execute();
            $carts_count2 = $this->countSavedCarts();
            $carts_deleted = $carts_count - $carts_count2;
            $this->logTask($carts_deleted.' carts has been deleted from #__jshopping_cart_temp');
        }
        catch (\Exception $exception)
        {
            $this->logTask($exception->getMessage(), $exception->getCode());
        }

        return $result ? Status::OK : Status::KNOCKOUT;
    }

    /**
     * Count rows in #__jshopping_cart_temp
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function countSavedCarts(): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(['COUNT(*)'])->from('#__jshopping_cart_temp');
        $result = (int) $db->setQuery($query)->loadResult();
        return $result;
    }
}
