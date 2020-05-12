<?php


namespace App\Service\DeviceNotifier;

use App\Entity\Post;

interface DeviceNotifierInterface
{
    /**
     * Notify latest post to Android and iOS devices.
     * @param Post $post
     * @param string $topic
     */
    public function notifyLatestPost(Post $post, string $topic): void;
}