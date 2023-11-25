<?php

namespace App;

use App\Entity\Comment;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    private $apiKey;

    private $endpoint;

    private $website;
    
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%env(AKISMET_KEY)%')]string $akismetKey,
        #[Autowire('%env(WEBSITE)%')]string $website,
    ) {
        $this->apiKey   = $akismetKey;
//        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
        $this->endpoint = 'https://rest.akismet.com/1.1/comment-check';
        $this->website  = $website;
    }

    /**
     * @param Comment $comment
     * @param array $context
     * @return int
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getSpamScore(Comment $comment, array $context)
    {
//        dd($this->verifyKey()->toArray());
//        dd($this->verifyKey()->getStatusCode(), $this->verifyKey()->getInfo(), $this->verifyKey()->getHeaders(), $this->verifyKey()->getContent());
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
                'api_key'               => $this->apiKey,
                'blog'                  => $this->website,
                'comment_type'          => 'comment',
                'comment_author'        => $comment->getAuthor(),
                'comment_author_email'  => $comment->getEmail(),
                'comment_content'       => $comment->getText(),
                'comment_date_gmt'      => $comment->getCreatedAt()->format('c'),
                'blog_lang'             => 'en',
                'blog_charset'          => 'UTF-8',
                'is_test'               => true,
            ]),
        ]);
//dd($response);
        $headers = $response->getHeaders();
//        dd($response->getStatusCode(), $headers, $response->getContent());
        // The book expects you to not have a valid api_key :)
        if ('discard' === ($headers['x-akismet-debug-help'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new RuntimeException(sprintf('Unable to check for spam: %s ($s)', $content, $headers['x-akismet-debug-help'][0]));
        }

        return 'true' === $content ? 1 : 0;
    }
    
    public function verifyKey()
    {
        $response = $this->client->request('POST', 'https://rest.akismet.com/1.1/verify-key', [
            'body' => [
                'blog'      => $this->website,
                'api_key'   => $this->apiKey
            ]
        ]);

        return $response;
    }
}