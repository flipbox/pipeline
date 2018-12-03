<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace flipbox\pipeline\processors;

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
     * @param array $extra
     *
     * @return mixed
     */
    public function process(array $stages, $payload, $extra = [])
    {
        if (!is_array($extra)) {
            $extra = ['source' => $extra];
        }

        return $this->processStages($stages, $payload, $extra);
    }

    /**
     * @param array $stages
     * @param mixed $payload
     * @param array $extra
     *
     * @return mixed
     */
    protected function processStages(array $stages, $payload, array $extra = [])
    {
        foreach ($stages as $stage) {
            $payload = $this->processStage($stage, $payload, $extra);
        }

        return $payload;
    }

    /**
     * @param callable $stage
     * @param mixed $payload
     * @param array $extra
     * @return mixed
     */
    protected function processStage(callable $stage, $payload, array $extra = [])
    {
        return call_user_func_array($stage, array_merge([$payload], $extra));
    }
}
