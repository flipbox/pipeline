<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/pipeline/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/pipeline
 */

namespace Flipbox\Pipeline\Pipelines;

/**
 * Supports the ability to define a pipe with an associated key.  This allows pipes to be overwritten and
 * handled by the processor in unique ways.
 *
 * @author Flipbox Digital <hello@flipboxdigital.com>
 * @since 1.0.0
 */
class KeyPipeline extends Pipeline
{
    /**
     * @inheritdoc
     */
    public function pipe(callable $stage, $key = null)
    {
        $pipeline = clone $this;

        if ($key === null) {
            $pipeline->stages[] = $stage;
        } else {
            $pipeline->stages[$key] = $stage;
        }

        return $pipeline;
    }
}
