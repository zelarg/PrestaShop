<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\LinkGroupColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\OrderPriceColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\ColorColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Creates definition for Orders grid
 */
final class OrderGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'order';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderCountriesChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatusesChoiceProvider;

    /**
     * @param HookDispatcherInterface $dispatcher
     * @param ConfigurationInterface $configuration
     * @param FormChoiceProviderInterface $orderCountriesChoiceProvider
     * @param FormChoiceProviderInterface $orderStatusesChoiceProvider
     */
    public function __construct(
        HookDispatcherInterface $dispatcher,
        ConfigurationInterface $configuration,
        FormChoiceProviderInterface $orderCountriesChoiceProvider,
        FormChoiceProviderInterface $orderStatusesChoiceProvider
    ) {
        parent::__construct($dispatcher);

        $this->configuration = $configuration;
        $this->orderCountriesChoiceProvider = $orderCountriesChoiceProvider;
        $this->orderStatusesChoiceProvider = $orderStatusesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Orders', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new DataColumn('id_order'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_order',
                ])
            )
            ->add((new DataColumn('reference'))
                ->setName($this->trans('Reference', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'reference',
                ])
            )
            ->add((new BooleanColumn('new'))
                ->setName($this->trans('New client', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'new',
                    'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                    'false_name' => $this->trans('No', [], 'Admin.Global'),
                ])
            )
            ->add((new DataColumn('customer'))
                ->setName($this->trans('Customer', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'customer',
                ])
            )
            ->add((new OrderPriceColumn('total_paid_tax_incl'))
                ->setName($this->trans('Total', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'total_paid_tax_incl',
                    'is_paid_field' => 'paid',
                ])
            )
            ->add((new DataColumn('payment'))
                ->setName($this->trans('Payment', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'payment',
                ])
            )
            ->add((new ColorColumn('osname'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'osname',
                    'color_field' => 'color',
                ])
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Date', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
            ->add((new LinkGroupColumn('pdf'))
                ->setName($this->trans('PDF', [], 'Admin.Global'))
                ->setOptions([
                        'links' => [
                            [
                                'icon' => 'picture_as_pdf',
                                'is_link_available_field' => 'is_invoice_available',
                                'route' => 'admin_orders_generate_invoice_pdf',
                                'route_param_name' => 'orderId',
                                'route_param_field' => 'id_order',
                            ],
                            [
                                'icon' => 'local_shipping',
                                'is_link_available_field' => 'delivery_number',
                                'route' => 'admin_orders_generate_delivery_slip_pdf',
                                'route_param_name' => 'orderId',
                                'route_param_field' => 'id_order',
                            ],
                        ],
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => $this->getRowActions(),
                ])
            )
        ;

        if ($this->orderCountriesChoiceProvider->getChoices()) {
            $columns->addAfter('new', (new DataColumn('country_name'))
                ->setName($this->trans('Delivery', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'country_name',
                ])
            );
        }

        if ($this->configuration->get('PS_B2B_ENABLE')) {
            $columns->addAfter('customer', (new DataColumn('company'))
                ->setName($this->trans('Company', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'company',
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = new FilterCollection();

        $filters
            ->add((new Filter('id_order', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_order')
            )
            ->add((new Filter('reference', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Reference', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('reference')
            )
            ->add((new Filter('new', YesAndNoChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('new')
            )
            ->add((new Filter('customer', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Customer', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('customer')
            )
            ->add((new Filter('company', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Company', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('company')
            )
            ->add((new Filter('total_paid_tax_incl', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Total', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('total_paid_tax_incl')
            )
            ->add((new Filter('payment', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Payment', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('payment')
            )
            ->add((new Filter('osname', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $this->orderStatusesChoiceProvider->getChoices(),
                ])
                ->setAssociatedColumn('osname')
            )
            ->add((new Filter('date_add', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('date_add')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search',
                    'reset_route_params' => [
                        'controller' => 'customer',
                        'action' => 'index',
                    ],
                    'redirect_route' => 'admin_customers_index',
                ])
                ->setAssociatedColumn('actions')
            )
        ;

        $orderCountriesChoices = $this->orderCountriesChoiceProvider->getChoices();

        if (!empty($orderCountriesChoices)) {
            $filters->add((new Filter('country_name', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $orderCountriesChoices,
                ])
                ->setAssociatedColumn('country_name')
            );
        }

        return $filters;
    }

    /**
     * @return RowActionCollection
     */
    private function getRowActions()
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('view'))
                    ->setName($this->trans('View', [], 'Admin.Actions'))
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => 'admin_orders_index',
                        'route_param_name' => 'id_order',
                        'route_param_field' => 'id_order',
                    ])
            )
        ;
    }
}
