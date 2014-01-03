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
 * @category    Tools
 * @package     unit_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Test\Tools\Migration\Acl\Db;

require_once realpath(__DIR__ . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Writer.php';

class WriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Writer
     */
    protected $_model;

    /**
     * DB adapter
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    protected function setUp()
    {
        $this->_adapterMock = $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract',
            array(),
            '',
            false,
            false,
            false,
            array('update')
        );
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Writer($this->_adapterMock, 'dummy');
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_adapterMock);
    }

    public function testUpdate()
    {
        $this->_adapterMock->expects($this->once())
            ->method('update')->with('dummy', array('resource_id' => 'new'), array('resource_id = ?' => 'old'));
        $this->_model->update('old', 'new');
    }
}

