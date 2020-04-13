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
        $params = [
            'data' => [
                'attributes' => [
                    'title'       => 'Pretty!',
                    'content'     => 'blabla',
                    'publishedAt' => '2020-03-05 20:34:45',
                ],
            ],
        ];

        return PageRequest::createFromBase(
            BaseRequest::create('', $method, $params, [], [], [], null)
        );
    }
}
