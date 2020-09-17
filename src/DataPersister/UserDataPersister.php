<?php

// @todo https://github.com/api-platform/docs/edit/2.5/core/data-persisters.md

namespace Noud\UserBundle\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Noud\UserBundle\Entity\User;
use Symfony\Component\Mailer\MailerInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $mailer;

    public function __construct(ContextAwareDataPersisterInterface $decorated, MailerInterface $mailer)
    {
        $this->decorated = $decorated;
        $this->mailer = $mailer;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        $result = $this->decorated->persist($data, $context);

        if (
            $data instanceof User && (
                ($context['collection_operation_name'] ?? null) === 'post' ||
                ($context['graphql_operation_name'] ?? null) === 'create'
            )
        ) {
            $this->sendWelcomeEmail($data);
        }

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }

    private function sendWelcomeEmail(User $user)
    {
        // @todo user welcome email
        // Your welcome email logic...
        // $this->mailer->send(...);
    }
}
