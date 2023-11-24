<?php

namespace App;

use App\Entity\Comment;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    private $endpoint;

    private $website;
    
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%env(AKISMET_KEY)%')]string $aksimetKey,
        #[Autowire('%env(WEBSITE)%')]string $website,
    ) {
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $aksimetKey);
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
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
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

        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-debug-help'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new RuntimeException(sprintf('Unable to check for spam: %s ($s)', $content, $headers['x-akismet-debug-help'][0]));
        }

        return 'true' === $content ? 1 : 0;
    }
}