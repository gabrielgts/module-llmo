<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Adds the LLMO AI-ready content fields to the product entity:
 *  - `llmo_ai_summary`        textarea, store scope
 *  - `llmo_ai_keywords`       textarea, store scope
 *  - `llmo_ai_use_cases`      textarea, store scope
 *  - `llmo_ai_faq`            textarea, store scope
 *  - `llmo_excluded_from_feed` boolean, store scope
 *
 * Idempotent: skips attributes that already exist.
 */
class AddLlmoProductAttributes implements DataPatchInterface
{
    private const GROUP_NAME = 'LLMO';
    private const GROUP_SORT = 80;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    // phpcs:ignore
    public function __construct(
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(): self
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ($this->textareaAttributes() as $code => $label) {
            $this->addTextareaIfMissing($eavSetup, $code, $label);
        }

        $this->addBooleanIfMissing(
            $eavSetup,
            'llmo_excluded_from_feed',
            'Exclude From LLMO Feed'
        );

        $this->ensureGroup($eavSetup);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Map of textarea attribute code => label.
     *
     * @return array<string, string>
     */
    private function textareaAttributes(): array
    {
        return [
            'llmo_ai_summary' => 'LLMO AI Summary',
            'llmo_ai_keywords' => 'LLMO AI Keywords',
            'llmo_ai_use_cases' => 'LLMO AI Use Cases',
            'llmo_ai_faq' => 'LLMO AI FAQ',
        ];
    }

    /**
     * Add a textarea attribute if not already present.
     *
     * @param EavSetup $eavSetup
     * @param string $code
     * @param string $label
     * @return void
     */
    private function addTextareaIfMissing(EavSetup $eavSetup, string $code, string $label): void
    {
        if ($eavSetup->getAttributeId(Product::ENTITY, $code)) {
            return;
        }

        $eavSetup->addAttribute(Product::ENTITY, $code, [
            'type' => 'text',
            'label' => $label,
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => false,
            'user_defined' => true,
            'visible' => true,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'group' => self::GROUP_NAME,
        ]);
    }

    /**
     * Add a boolean attribute if not already present.
     *
     * @param EavSetup $eavSetup
     * @param string $code
     * @param string $label
     * @return void
     */
    private function addBooleanIfMissing(EavSetup $eavSetup, string $code, string $label): void
    {
        if ($eavSetup->getAttributeId(Product::ENTITY, $code)) {
            return;
        }

        $eavSetup->addAttribute(Product::ENTITY, $code, [
            'type' => 'int',
            'label' => $label,
            'input' => 'boolean',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => false,
            'user_defined' => true,
            'visible' => true,
            'default' => '0',
            'used_in_product_listing' => true,
            'group' => self::GROUP_NAME,
        ]);
    }

    /**
     * Make sure the LLMO attribute group exists on every attribute set.
     *
     * @param EavSetup $eavSetup
     * @return void
     */
    private function ensureGroup(EavSetup $eavSetup): void
    {
        $entityTypeId = (int) $eavSetup->getEntityTypeId(Product::ENTITY);
        foreach ($eavSetup->getAllAttributeSetIds($entityTypeId) as $attributeSetId) {
            $eavSetup->addAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                self::GROUP_NAME,
                self::GROUP_SORT
            );
        }
    }
}
