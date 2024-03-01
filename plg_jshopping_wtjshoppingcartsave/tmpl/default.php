<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
/**
 * @var $products_saved_cart array
 * @var $products_wishlist   array
 * @var $jshopConfig         object JoomShopping config
 */
$jshopConfig = \JSFactory::getConfig();

if (!class_exists('JSHelper') && file_exists(JPATH_SITE . '/components/com_jshopping/bootstrap.php'))
{
	require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
}

$current_lang = \JSFactory::getLang()->lang;
\JSFactory::loadLanguageFile($current_lang);

$products_count          = count($products_saved_cart);
$wishlist_products_count = count($products_wishlist);

$products_count_label          = '<span class="badge bg-' . (($products_count > 0) ? 'danger' : 'dark') . ' ms-auto">' . $products_count . '</span>';
$wishlist_products_count_label = '<span class="badge bg-' . (($wishlist_products_count > 0) ? 'danger' : 'dark') . ' ms-auto">' . $wishlist_products_count . '</span>';
?>
<div class="main-card my-3">
	<?php echo HTMLHelper::_('uitab.startTabSet', 'wtjshoppingcartsave', ['active' => 'cart', 'recall' => true, 'breakpoint' => 768]); ?>
	<?php $products_count_label = Text::_('JSHOP_CART') . ' ' . $products_count_label; ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'wtjshoppingcartsave', 'cart', $products_count_label); ?>
	<?php if (!empty($products_saved_cart)):
        if(!empty($products_saved_cart_date_modified)):?>

            <div class="text-muted"><?php echo Text::_('JGLOBAL_FIELD_MODIFIED_LABEL').': '. $products_saved_cart_date_modified; ?></div>

        <?php

        endif;
		foreach ($products_saved_cart as $product): ?>
            <div class="row border-bottom py-2">
                <div class="col-12 col-md-3 col-xl-2">
					<?php

					$product_image = $jshopConfig->noimage;
					if ($product['thumb_image'])
					{
						$product_image = $product['thumb_image'];
					}

					$img_attribs = [
						'width' => '120',
						'class' => 'img-fluid img-thumbnail',
					];
					echo HTMLHelper::image(\JSHelper::getPatchProductImage($product_image, '', 1), $product['product_name'], $img_attribs);
					?>
                </div>
                <div class="col-12 col-md-8 col-xl-9">
                    <hgroup>
                        <h4><?php echo $product['product_name']; ?></h4>
						<?php if (!empty($product['ean']) ||
							!empty($product['manufacturer_code']) ||
							!empty($product['real_ean'])): ?>
                            <h5>
								<?php if (!empty($product['ean'])): ?>
                                    <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_EAN_PRODUCT'); ?>: <?php echo $product['ean']; ?> </span>
								<?php endif; ?>
								<?php if (!empty($product['manufacturer_code'])): ?>
                                    <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_MANUFACTURER_CODE'); ?>: <?php echo $product['manufacturer_code']; ?></span>
								<?php endif; ?>
								<?php if (!empty($product['real_ean'])): ?>
                                    <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_EAN'); ?>: <?php echo $product['real_ean']; ?></span>
								<?php endif; ?>
                            </h5>
						<?php endif; ?>
                    </hgroup>
					<?php if (array_key_exists('attributes_value', $product) && !empty($product['attributes_value'])): ?>
                        <ul class="list-group list-group-horizontal">
							<?php foreach ($product['attributes_value'] as $attr): ?>
                                <li class="list-group-item">
                                    <span class="fw-bold"><?php echo $attr->attr; ?>:</span> <?php echo $attr->value; ?>
                                </li>
							<?php endforeach; ?>
                        </ul>
					<?php endif; ?>
                </div>
                <div class="col-12 col-md-1">
					<?php
					$url = 'index.php?option=com_jshopping&controller=products&task=edit&product_id=' . $product['product_id'];
					echo HTMLHelper::link($url, Text::_('JSHOP_PRODUCT'), ['target' => '_blank', 'class' => 'btn btn-sm btn-primary']); ?>
                </div>
            </div>
		<?php
		endforeach;
	endif;
	?>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php if ($jshopConfig->enable_wishlist): ?>
		<?php $wishlist_products_count_label = Text::_('JSHOP_WISHLIST') . ' ' . $wishlist_products_count_label; ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'wtjshoppingcartsave', 'wishlist', $wishlist_products_count_label); ?>
		<?php if (!empty($products_wishlist)):
			foreach ($products_wishlist as $product): ?>
                <div class="row border-bottom py-2">
                    <div class="col-12 col-md-3 col-xl-2">
						<?php

						$product_image = $jshopConfig->noimage;
						if ($product['thumb_image'])
						{
							$product_image = $product['thumb_image'];
						}

						$img_attribs = [
							'width' => '120',
							'class' => 'img-fluid img-thumbnail',
						];
						echo HTMLHelper::image(\JSHelper::getPatchProductImage($product_image, '', 1), $product['product_name'], $img_attribs);
						?>
                    </div>
                    <div class="col-12 col-md-8 col-xl-9">
                        <hgroup>
                            <h4><?php echo $product['product_name']; ?></h4>
							<?php if (!empty($product['ean']) ||
								!empty($product['manufacturer_code']) ||
								!empty($product['real_ean'])): ?>
                                <h5>
									<?php if (!empty($product['ean'])): ?>
                                        <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_EAN_PRODUCT'); ?>: <?php echo $product['ean']; ?> </span>
									<?php endif; ?>
									<?php if (!empty($product['manufacturer_code'])): ?>
                                        <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_MANUFACTURER_CODE'); ?>: <?php echo $product['manufacturer_code']; ?></span>
									<?php endif; ?>
									<?php if (!empty($product['real_ean'])): ?>
                                        <span class="badge bg-light text-dark"><?php echo Text::_('JSHOP_EAN'); ?>: <?php echo $product['real_ean']; ?></span>
									<?php endif; ?>
                                </h5>
							<?php endif; ?>

                        </hgroup>
						<?php if (array_key_exists('attributes_value', $product) && !empty($product['attributes_value'])): ?>
                            <ul class="list-group list-group-horizontal">
								<?php foreach ($product['attributes_value'] as $attr): ?>
                                    <li class="list-group-item">
                                        <span class="fw-bold"><?php echo $attr->attr; ?>:</span> <?php echo $attr->value; ?>
                                    </li>
								<?php endforeach; ?>
                            </ul>
						<?php endif; ?>
                    </div>
                    <div class="col-12 col-md-1">
						<?php
						$url = 'index.php?option=com_jshopping&controller=products&task=edit&product_id=' . $product['product_id'];
						echo HTMLHelper::link($url, Text::_('JSHOP_PRODUCT'), ['target' => '_blank', 'class' => 'btn btn-sm btn-primary']); ?>
                    </div>
                </div>
			<?php
			endforeach;
		endif;
		?>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php endif; ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
</div>

