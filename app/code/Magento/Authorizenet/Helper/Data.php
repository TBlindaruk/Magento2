<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Authorizenet\Helper;

/**
 * Authorizenet Data Helper
 */
class Data extends \Magento\App\Helper\AbstractHelper implements HelperInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Set secure url checkout is secure for current store.
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        $params['_type'] = \Magento\Core\Model\Store::URL_TYPE_LINK;
        if (isset($params['is_secure'])) {
            $params['_secure'] = (bool)$params['is_secure'];
        } elseif ($this->_storeManager->getStore()->isCurrentlySecure()) {
            $params['_secure'] = true;
        }
        return parent::_getUrl($route, $params);
    }

    /**
     * Retrieve save order url params
     *
     * @param string $controller
     * @return array
     */
    public function getSaveOrderUrlParams($controller)
    {
        $route = array();
        switch ($controller) {
            case 'onepage':
                $route['action'] = 'saveOrder';
                $route['controller'] = 'onepage';
                $route['module'] = 'checkout';
                break;

            case 'sales_order_create':
            case 'sales_order_edit':
                $route['action'] = 'save';
                $route['controller'] = 'sales_order_create';
                $route['module'] = 'admin';
                break;

            default:
                break;
        }

        return $route;
    }

    /**
     * Retrieve redirect ifrmae url
     *
     * @param array params
     * @return string
     */
    public function getRedirectIframeUrl($params)
    {
        switch ($params['controller_action_name']) {
            case 'onepage':
                $route = 'authorizenet/directpost_payment/redirect';
                break;

            default:
                $route = 'authorizenet/directpost_payment/redirect';
                break;
        }

        return $this->_getUrl($route, $params);
    }

    /**
     * Retrieve place order url on front
     *
     * @return  string
     */
    public function getPlaceOrderFrontUrl()
    {
        return $this->_getUrl('authorizenet/directpost_payment/place');
    }

    /**
     * Retrieve place order url
     *
     * @param array params
     * @return  string
     */
    public function getSuccessOrderUrl($params)
    {
        $param = array();
        switch ($params['controller_action_name']) {
            case 'onepage':
                $route = 'checkout/onepage/success';
                break;

            default :
                $route = 'checkout/onepage/success';
                break;
        }

        return $this->_getUrl($route, $param);
    }

    /**
     * Update all child and parent order's edit increment numbers.
     * Needed for Admin area.
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function updateOrderEditIncrements(\Magento\Sales\Model\Order $order)
    {
        if ($order->getId() && $order->getOriginalIncrementId()) {
            $collection = $order->getCollection();
            $quotedIncrId = $collection->getConnection()->quote($order->getOriginalIncrementId());
            $collection->getSelect()->where(
                "original_increment_id = {$quotedIncrId} OR increment_id = {$quotedIncrId}"
            );

            foreach ($collection as $orderToUpdate) {
                $orderToUpdate->setEditIncrement($order->getEditIncrement());
                $orderToUpdate->save();
            }
        }
    }
}
