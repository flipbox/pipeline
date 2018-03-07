<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Processors;

use League\Pipeline\ProcessorInterface;

/**
 * A base pipeline built for extensibility
 *
 * @author Flipbox Digital <hello@flipboxdigital.com>
 * @since 1.0.0
 */
class Processor implements ProcessorInterface
{
    /**
     * @param array $stages
     * @param mixed $payload
     * @param mixed|null $source
     *
     * @return mixed
     */
    public function process(array $stages, $payload, $source = null)
    {
        return $this->processStages($stages, $payload, $source ?: $payload);
    }

    /**
     * @param array $stages
     * @param mixed $payload
     * @param mixed $source
     *
     * @return mixed
     */
    protected function processStages(array $stages, $payload, $source)
    {
        foreach ($stages as $stage) {
            $payload = $this->processStage($stage, $payload, $source);
        }

        return $payload;
    }

    /**
     * @param callable $stage
     * @param mixed $payload
     * @param mixed|null $source
     * @return mixed
     */
    protected function processStage(callable $stage, $payload, $source = null)
    {
        return call_user_func_array($stage, [$payload, $source]);
    }
}
