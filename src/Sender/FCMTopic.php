<?php

namespace LaravelFCM\Sender;

use LaravelFCM\Request\TopicRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FCMGroup.
 */
class FCMTopic extends HTTPSender
{

    private $add_subscription_url = 'https://iid.googleapis.com/iid/v1:batchAdd';
    private $remove_subscription_url = 'https://iid.googleapis.com/iid/v1:batchRemove';

    /**
     * Create a topic.
     *
     * @param       $notificationKeyName
     * @param array $registrationIds
     *
     * @return null|string notification_key
     */
    public function createTopic($topic_id, $registration_id)
    {
        $request = new TopicRequest('create', $topic_id);
        if(is_array($registration_id)){
            return null;
        }
        return $this->client->request('post', $this->url.$registration_id."/rel/topics/".$topic_id, $request->build());

        // return $this->getNotificationToken($response);
    }

    /**
     * add subscription to a topic.
     *
     * @param       $topic_id
     * @param       $recipients_tokens
     * @return null|string notification_key
     */
    public function subscribeTopic($topic_id, $recipients_tokens)
    {
        $request = new TopicRequest('subscribe', $topic_id, $recipients_tokens);
        $response = $this->client->request('post', $this->add_subscription_url, $request->build());

        return $this->getNotificationToken($response);
    }

    /**
     * remove subscription from a topic.
     *
     *
     * @param       $topic_id
     * @param       $recipients_tokens
     * @return null|string notification_key
     */
    public function unsubscribeTopic($topic_id, $recipients_tokens)
    {
        $request = new TopicRequest('unsubscribe', $topic_id, $recipients_tokens);
        $response = $this->client->request('post', $this->remove_subscription_url, $request->build());

        return $this->getNotificationToken($response);
    }

    /**
     * @internal
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return null|string notification_key
     */
    private function getNotificationToken(ResponseInterface $response)
    {
        if (! $this->isValidResponse($response)) {
            return null;
        }

        $json = json_decode($response->getBody()->getContents(), true);

        return $json['notification_key'];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function isValidResponse(ResponseInterface $response)
    {
        return $response->getStatusCode() === 200;
    }
}
