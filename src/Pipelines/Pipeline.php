<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Pipelines;

use League\Pipeline\FingersCrossedProcessor;
use League\Pipeline\PipelineInterface;
use League\Pipeline\ProcessorInterface;
use League\Pipeline\StageInterface;
use Psr\Log\InvalidArgumentException;

/**
 * An extensible pipeline
 *
 * @author Flipbox Digital <hello@flipboxdigital.com>
 * @since 1.0.0
 */
class Pipeline implements PipelineInterface
{
    /**
     * @var StageInterface[]|callable[]
     */
    protected $stages = [];

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param callable[] $stages
     * @param ProcessorInterface $processor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $stages = [], ProcessorInterface $processor = null)
    {
        foreach ($stages as $stage) {
            if (false === is_callable($stage)) {
                throw new InvalidArgumentException('All stages should be callable.');
            }
        }

        $this->stages = $stages;
        $this->processor = $processor ?: new FingersCrossedProcessor;
    }

    /**
     * @inheritdoc
     */
    public function pipe(callable $stage)
    {
        $pipeline = clone $this;
        $pipeline->stages[] = $stage;

        return $pipeline;
    }

    /**
     * Process the payload.
     *
     * @param $payload
     *
     * @return mixed
     */
    public function process($payload)
    {
        return $this->processor->process($this->stages, $payload);
    }

    /**
     * @inheritdoc
     */
    public function __invoke($payload)
    {
        return $this->process($payload);
    }
}
