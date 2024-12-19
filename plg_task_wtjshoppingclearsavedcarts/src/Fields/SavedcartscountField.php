<?php
/**
 * @package    WT JShopping cart save
 * @version       1.1.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (C) 2024 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

namespace Joomla\Plugin\Task\Wtjshoppingclearsavedcarts\Fields;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\NoteField;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;


class SavedcartscountField extends NoteField
{

    protected $type = 'Savedcartscount';


    /**
     * Method to get the field input markup for a spacer.
     * The spacer does not have accept input.
     *
     * @return  string  The field input markup.
     *
     * @since   1.7.0
     */
    protected function getInput()
    {
        $count = Factory::getApplication()
                         ->bootPlugin('wtjshoppingclearsavedcarts','task')
                         ->countSavedCarts();

        return Text::sprintf('PLG_WTJSHOPPINGCLEARSAVEDCARTS_SAVED_CARTS_COUNT_INFO', $count);
    }

}