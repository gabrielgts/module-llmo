<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

use Gtstudio\Llmo\Api\Data\ValidationIssueInterface;
use Gtstudio\Llmo\Api\Data\ValidationResultInterface;
use Gtstudio\Llmo\Api\Validator\SchemaValidatorInterface;
use JsonSchema\Validator;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\DriverInterface;
use Psr\Log\LoggerInterface;

/**
 * Validates an ACP feed body against the bundled `etc/schema/acp-feed.json`
 * draft-07 JSON Schema using `justinrainbow/json-schema`.
 *
 * Falls back to a single fatal issue when the body cannot be decoded or the
 * schema file cannot be read.
 */
class AcpSchemaValidator implements SchemaValidatorInterface
{
    public const SOURCE = 'schema';

    private const SCHEMA_RELATIVE_PATH = 'etc/schema/acp-feed.json';

    /**
     * @param ComponentRegistrar $componentRegistrar
     * @param DriverInterface $filesystemDriver
     * @param LoggerInterface $logger
     * @param ValidationResultBuilder $resultBuilder
     */
    // phpcs:ignore
    public function __construct(
        private readonly ComponentRegistrar $componentRegistrar,
        private readonly DriverInterface $filesystemDriver,
        private readonly LoggerInterface $logger,
        private readonly ValidationResultBuilder $resultBuilder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validateBody(string $exporterCode, string $body): ValidationResultInterface
    {
        $decoded = \json_decode($body);
        if ($decoded === null && \json_last_error() !== \JSON_ERROR_NONE) {
            return $this->resultBuilder->build($exporterCode, [[
                'severity' => ValidationIssueInterface::SEVERITY_ERROR,
                'source' => self::SOURCE,
                'code' => 'invalid_json',
                'message' => 'Feed body is not valid JSON: ' . \json_last_error_msg(),
                'path' => null,
            ]]);
        }

        $schemaPath = $this->resolveSchemaPath();
        if ($schemaPath === null) {
            return $this->resultBuilder->build($exporterCode, [[
                'severity' => ValidationIssueInterface::SEVERITY_ERROR,
                'source' => self::SOURCE,
                'code' => 'schema_missing',
                'message' => 'ACP schema file not found on disk.',
                'path' => null,
            ]]);
        }

        $validator = new Validator();
        $validator->validate($decoded, (object) ['$ref' => 'file://' . $schemaPath]);

        if ($validator->isValid()) {
            return $this->resultBuilder->build($exporterCode, []);
        }

        $issues = [];
        foreach ($validator->getErrors() as $error) {
            $issues[] = [
                'severity' => ValidationIssueInterface::SEVERITY_ERROR,
                'source' => self::SOURCE,
                'code' => (string) ($error['constraint'] ?? 'schema_violation'),
                'message' => (string) ($error['message'] ?? 'Schema violation'),
                'path' => (string) ($error['property'] ?? ''),
            ];
        }

        return $this->resultBuilder->build($exporterCode, $issues);
    }

    /**
     * Resolve the bundled ACP schema's absolute path on disk.
     *
     * @return string|null
     */
    private function resolveSchemaPath(): ?string
    {
        $modulePath = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            'Gtstudio_Llmo'
        );
        if ($modulePath === null) {
            $this->logger->error('[Gtstudio_Llmo] Module path not registered for schema lookup.');
            return null;
        }

        $absolute = $modulePath . '/' . self::SCHEMA_RELATIVE_PATH;
        try {
            if (!$this->filesystemDriver->isExists($absolute)) {
                $this->logger->error('[Gtstudio_Llmo] ACP schema not found', ['path' => $absolute]);
                return null;
            }
        } catch (\Throwable $th) {
            $this->logger->error('[Gtstudio_Llmo] ACP schema lookup failed', [
                'exception' => $th,
                'context' => ['path' => $absolute],
            ]);
            return null;
        }
        return $absolute;
    }
}
