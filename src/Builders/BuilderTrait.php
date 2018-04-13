<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Builders;

use Flipbox\Pipeline\Pipelines\Pipeline;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use League\Pipeline\PipelineInterface;
use League\Pipeline\ProcessorInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Build a config based Pipeline
 *
 * @author Flipbox Digital <hello@flipboxdigital.com>
 * @since 1.0.0
 */
trait BuilderTrait
{
    use AutoLoggerTrait;

    /**
     * @var callable[]
     */
    private $stages = [];

    /**
     * Add an stage.
     *
     * @param callable $stage
     *
     * @return $this
     */
    public function add(callable $stage)
    {
        $this->stages[] = $stage;

        return $this;
    }

    /**
     * Build a new Pipeline object
     *
     * @param  ProcessorInterface|null $processor
     *
     * @return PipelineInterface
     */
    public function build(ProcessorInterface $processor = null): PipelineInterface
    {
        try {
            $config = [
                'stages' => $this->stages
            ];

            if ($processor !== null) {
                $config['processor'] = $processor;
            }
            return $this->createPipeline(
                $this->prepareConfig($config)
            );
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * @param array $config
     * @return array
     */
    protected function prepareConfig(array $config = []): array
    {
        return $config;
    }

    /**
     * @param array $config
     * @return PipelineInterface
     */
    protected function createPipeline(array $config = []): PipelineInterface
    {
        return new Pipeline($config);
    }
}
