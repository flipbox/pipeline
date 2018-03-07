<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Processors;

/**
 * A base pipeline which allows processing of specific stage keys.
 *
 * @author Flipbox Digital <hello@flipboxdigital.com>
 * @since 1.0.0
 */
class SelectableStageProcessor extends Processor
{
    /**
     * @var array
     */
    public $keys = [];

    /**
     * If keys are empty, proceed processing all stages.
     *
     * @var bool
     */
    public $processOnEmpty = false;

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
     * @param mixed $source
     *
     * @return mixed
     */
    protected function processStages(array $stages, $payload, $source)
    {
        foreach ($stages as $key => $stage) {
            if ($this->isStageSelectable($key)) {
                $payload = $this->processStage($stage, $payload, $source);
            }
        }

        return $payload;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isStageSelectable($key)
    {
        if (empty($this->keys) && $this->processOnEmpty === true) {
            return true;
        }

        if (is_int($key) && $this->autoSelect === true) {
            return true;
        }

        return $this->stageMatches($key) || $this->stageBeginsWith($key);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function stageBeginsWith($key): bool
    {
        foreach($this->keys as $needle) {
            if(0 === strpos($key, $needle . ':')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function stageMatches($key): bool
    {
        return in_array($key, (array)$this->keys, true);
    }
}
