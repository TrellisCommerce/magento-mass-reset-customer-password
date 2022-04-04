<?php
/**
 * @author    Trellis Dev Team
 * @copyright Copyright (c) Trellis.co (https://trellis.co/)
 * @package   CustomerResetPassword
 */

namespace Trellis\CustomerResetPassword\Controller\Adminhtml\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassResetPassword
 * @package Trellis\CustomerResetPassword\Controller\Adminhtml\Index
 */
class MassResetPassword extends \Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction
{
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Context                     $context
     * @param Filter                      $filter
     * @param CollectionFactory           $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param AbstractCollection $collection
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersResetPassword = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $customer = $this->customerRepository->getById($customerId);

            try {
                $this->customerAccountManagement->initiatePasswordReset(
                    $customer->getEmail(),
                    \Magento\Customer\Model\AccountManagement::EMAIL_REMINDER,
                    $customer->getWebsiteId()
                );

                // Mark sucessful reset password email sent.
                $customersResetPassword++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while sending reset password email.')
                );
            }
        }

        // The grid page would reload with a success/error message(s)
        if ($customersResetPassword) {
            $this->messageManager->addSuccessMessage(
                __(
                    'The total %1 customer(s) will receive email with a link to reset password.',
                    $customersResetPassword
                )
            );
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}