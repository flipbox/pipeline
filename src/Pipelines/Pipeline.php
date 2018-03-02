<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Pipelines;

use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Skeleton\Helpers\ObjectHelper;
use Flipbox\Skeleton\Object\AbstractObject;
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
class Pipeline extends AbstractObject implements PipelineInterface
{
    /**
     * @var StageInterface[]|callable[]
     */
    protected $stages = [];

    /**
     * @var ProcessorInterface|null
     */
    protected $processor = FingersCrossedProcessor::class;

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $this->stages = ArrayHelper::remove($config, 'stages', []);
        parent::__construct($config);
    }

    /**
     * @return callable[]|StageInterface[]|mixed|null
     */
    protected function getStages()
    {
        foreach ($this->stages as $key => $stage) {
            $this->stages[$key] = $this->resolveStage($stage);
        }

        return $this->stages;
    }

    /**
     * @param $stage
     * @return callable
     * @throws InvalidArgumentException
     */
    protected function resolveStage($stage): callable
    {
        if (is_callable($stage)) {
            return $stage;
        }

        try {
            if (!is_object($stage)) {
                return $this->resolveStage(
                    ObjectHelper::create($stage)
                );
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        throw new InvalidArgumentException('All stages should be callable.');
    }

    /**
     * @return ProcessorInterface
     * @throws InvalidArgumentException
     */
    public function getProcessor(): ProcessorInterface
    {
        if (!$this->processor instanceof ProcessorInterface) {
            $this->processor = $this->resolveProcessor($this->processor);
        }

        return $this->processor;
    }

    /**
     * @param $processor
     * @return $this
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * @param $processor
     * @return ProcessorInterface
     * @throws InvalidArgumentException
     */
    protected function resolveProcessor($processor): ProcessorInterface
    {
        if ($processor instanceof ProcessorInterface) {
            return $processor;
        }

        try {
            if (!is_object($processor)) {
                return $this->resolveProcessor(
                    ObjectHelper::create($processor)
                );
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        throw new InvalidArgumentException(sprintf(
            "Processor must be an instance of '%s'.",
            ProcessorInterface::class
        ));
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
     * @param $payload
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function process($payload)
    {
        return $this->getProcessor()->process($this->getStages(), $payload);
    }

    /**
     * @inheritdoc
     */
    public function __invoke($payload)
    {
        return $this->process($payload);
    }
}
