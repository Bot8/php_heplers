<?php

/**
 * Class JsonStructurer
 */
class JsonStructurer
{
    /** @var array  */
    protected $structure = [];

    /** @var \Closure[]  */
    protected $skipKeyCallbacks;

    public function getStructure(): array
    {
        return $this->structure;
    }

    /**
     * JsonTester constructor.
     *
     * @param array $data
     * @param array $skipKeyCallbacks
     */
    public function __construct(array $data, array $skipKeyCallbacks = [])
    {
        $this->skipKeyCallbacks = $skipKeyCallbacks;

        $this->parse($data);
    }

    protected function skipKey($key)
    {
        if (is_numeric($key)) {
            return true;
        }

        foreach ($this->skipKeyCallbacks as $callback) {
            if ($callback($key)) {
                return true;
            }
        }

        return false;
    }

    protected function parse($array, $arrayKey = '')
    {
        foreach ($array as $key => $value) {

            if ($this->skipKey($key)) {
                $key = '*';
            }

            $itemKey = empty($arrayKey) ? $key : "$arrayKey.$key";

            if (is_array($value) && !empty($value)) {
                $this->parse($value, $itemKey);
            } else {
                $this->put($arrayKey, $key);
            }
        }
    }

    protected function put($storageKey, $value)
    {
        if (!isset($this->structure[$storageKey])) {
            $this->structure[$storageKey] = [$value];
        } else {
            if (!in_array($value, $this->structure[$storageKey])) {
                $this->structure[$storageKey][] = $value;
            }
        }
    }
}