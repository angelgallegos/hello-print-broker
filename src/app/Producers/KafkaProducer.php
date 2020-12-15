<?php

namespace App\Producers;

use App\Entities\Request;
use Framework\Utils\Configuration\ConfigurationInterface;
use Kafka\Exception;
use Kafka\Producer;
use Kafka\ProducerConfig;
use Monolog\Logger;

class KafkaProducer
{
    /**
     * @var Producer|null
     */
    private ?Producer $producer = null;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var ConfigurationInterface
     */
    private ConfigurationInterface $config;

    public function __construct(
        Logger $logger,
        ConfigurationInterface $config
    ) {
        $this->config = $config;
        $this->logger = $logger;

        $producerConfig = ProducerConfig::getInstance();
        $producerConfig->setMetadataRefreshIntervalMs($this->config->get("KAFKA_METADATA_REFRESH_INTERVAL", 10000));
        $producerConfig->setMetadataBrokerList("{$this->config->get("KAFKA_URL")}:{$this->config->get("KAFKA_PORT")}");
        $producerConfig->setBrokerVersion($this->config->get("KAFKA_BROKER_VERSION"));
        $producerConfig->setRequiredAck($this->config->get("KAFKA_ACKNOWLEDGMENT", 1));
        $producerConfig->setIsAsyn($this->config->get("KAFKA_IS_ASYN", false));
        $producerConfig->setProduceInterval($this->config->get("KAFKA_PRODUCE_INTERVAL", 500));
        try {
            $this->producer = new Producer();
            $this->producer->setLogger($logger);
        } catch (Exception $e) {
            $this->logger->alert(
                "The next error occurred while sending the message to Kafka: ".$e->getMessage()
            );
        }

    }

    /**
     * @param Request $request
     * @param string $topic
     * @return bool
     */
    public function send(
        Request $request,
        string $topic = 'topic_a'
    ): bool {
        if (!$this->producer) {
            return false;
        }

        $this->producer->send([
            [
                'topic' => $topic,
                'value' => json_encode($request),
                'key' => '',
            ],
        ]);

        return true;
    }
}