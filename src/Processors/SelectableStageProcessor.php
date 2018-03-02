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
class SelectableStageProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    public $keys = [];

    /**
     * If a stage key is numeric, it's considered anonymous.  If true, anonymous stages will automatically
     * be included in the process.
     *
     * @var bool
     */
    public $autoSelect = true;

    /**
     * SelectableStageProcessor constructor.
     * @param array $keys
     */
    public function __construct(array $keys = [])
    {
        $this->keys = $keys;
    }

    /**
     * @param array $stages
     * @param mixed $payload
     *
     * @return mixed
     */
    public function process(array $stages, $payload)
    {
        foreach ($stages as $key => $stage) {
            if ($this->isStageSelectable($key)) {
                $payload = call_user_func($stage, $payload);
            }
        }

        return $payload;
    }

    /**
     * @param $key
     * @return bool
     */
    private function isStageSelectable($key)
    {
        if (empty($this->keys)) {
            return false;
        }

        if (is_int($key) && $this->autoSelect === true) {
            return true;
        }

        return in_array($key, (array)$this->keys, true);
    }
}
