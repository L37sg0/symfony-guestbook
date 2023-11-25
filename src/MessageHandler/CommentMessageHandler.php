<?php

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpamChecker $spamChecker,
        private CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        $spamScore = $this->spamChecker->getSpamScore($comment, $message->getContext());

        if (1 === $spamScore) {
            $comment->setState('spam');
        } else if (2 === $spamScore) {
            $comment->setState('submitted');
        } else {
            $comment->setState('published');
        }

        $this->entityManager->flush();
    }
}