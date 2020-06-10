<?php

namespace App\Service\DeviceNotifier;

use App\Entity\Post;
use DateTime;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;

class FcmService implements DeviceNotifierInterface
{
    private $messaging;
    private $logger;

    public function __construct(Messaging $messaging, LoggerInterface $logger)
    {
        $this->messaging = $messaging;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function notifyPost(Post $post, string $topic = "actualite"): void
    {
        $this->logger->info('"START NOTIFICATION"');
        $published = $post->getPublished();
        $updated = $post->getUpdated();
        $notification = Notification::create(
            "Un nouvel article est disponible !",
            $post->getSubtitle() ?? ""
        );
        $data = [
            'id' => $post->getId(),
            'published' => is_null($published) ? null : $published->format(DateTime::ISO8601),
            'updated' => is_null($updated) ? null : $updated->format(DateTime::ISO8601),
            'url' => $post->getUrl(),
            'title' => $post->getTitle(),
            'subtitle' => $post->getSubtitle(),
        ];

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification)
            ->withData($data);

        try {
            $this->messaging->send($message);
            $this->logger->info('"NOTIFICATION SENT"');
        } catch (MessagingException $e) {
            $this->logger->error($e);
        } catch (FirebaseException $e) {
            $this->logger->error($e);
        }
    }

    public function notify(string $title, string $body, array $data = [], string $topic = "actualite"): void
    {
        $this->logger->info('"START NOTIFICATION"');
        $notification = Notification::create(
            $title,
            $body ?? ""
        );

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification);

        if ($data) {
            $message = $message->withData($data);
        }

        try {
            $this->messaging->send($message);
            $this->logger->info('"NOTIFICATION SENT"');
        } catch (MessagingException $e) {
            $this->logger->error($e);
        } catch (FirebaseException $e) {
            $this->logger->error($e);
        }
    }
}
