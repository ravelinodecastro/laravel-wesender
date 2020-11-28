<?php

namespace Ravelino\Wesender;

abstract class WesenderMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * @var null|string
     */
    public $statusCallback;

    /**
     * @var null|string
     */
    public $statusCallbackMethod;

    /**
     * Create a message object.
     * @param string $content
     * @return static
     */
    public static function create(string $content = ''): self
    {
        return new static($content);
    }

    /**
     * Create a new message instance.
     *
     * @param  string $content
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string $content
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the status callback.
     *
     * @param string $statusCallback
     * @return $this
     */
    public function statusCallback(string $statusCallback): self
    {
        $this->statusCallback = $statusCallback;

        return $this;
    }

    /**
     * Set the status callback request method.
     *
     * @param string $statusCallbackMethod
     * @return $this
     */
    public function statusCallbackMethod(string $statusCallbackMethod): self
    {
        $this->statusCallbackMethod = $statusCallbackMethod;

        return $this;
    }
}
