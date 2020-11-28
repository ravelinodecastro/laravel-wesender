<?php

namespace Ravelino\Wesender;

class WesenderConfig
{
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    public function getApiKey(): ?string
    {
        return $this->config['api_key'] ?? null;
    }
    public function getSpecialCharacters(): ?string
    {
        return $this->config['special_characters'] ?? null;
    }
}
