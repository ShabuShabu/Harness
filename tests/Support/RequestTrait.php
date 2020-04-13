<?php


namespace ShabuShabu\Harness\Tests\Support;


use ShabuShabu\Harness\Request;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

trait RequestTrait
{
    /**
     * @param string $method
     * @return \ShabuShabu\Harness\Request
     */
    protected function request(string $method = 'POST'): Request
    {
        return PageRequest::createFromBase(
            BaseRequest::create('', $method, [], [], [], [], null)
        );
    }
}
