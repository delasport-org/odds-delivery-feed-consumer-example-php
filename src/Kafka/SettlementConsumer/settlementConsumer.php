<?php
$bootstrapServers = getenv('BOOTSTRAP_SERVERS', true) ?: getenv('BOOTSTRAP_SERVERS');
$saslMechanism = getenv('SASL_MECHANISM', true) ?: getenv('SASL_MECHANISM');
$securityProtocol = getenv('SECURITY_PROTOCOL', true) ?: getenv('SECURITY_PROTOCOL');
$saslUsername = getenv('SASL_USERNAME', true) ?: getenv('SASL_USERNAME');
$saslPassword = getenv('SASL_PASSWORD', true) ?: getenv('SASL_PASSWORD');
$clientId = getenv('CLIENT_ID', true) ?: getenv('CLIENT_ID');
$groupId = getenv('GROUP_ID', true) ?: getenv('GROUP_ID');
$autoOffset  = getenv('AUTO_OFFSET_RESET', true) ?: getenv('AUTO_OFFSET_RESET');
$settlementTopic = getenv('SETTLEMENT_TOPIC', true) ?: getenv('SETTLEMENT_TOPIC');


$conf = new RdKafka\Conf();
$conf->set('bootstrap.servers', $bootstrapServers );
$conf->set('sasl.mechanism', $saslMechanism);
$conf->set('security.protocol', $securityProtocol);
$conf->set('sasl.username', $saslUsername);
$conf->set('sasl.password', $saslPassword);
$conf->set('client.id', $clientId);
$conf->set('group.id', $groupId);
$conf->set('auto.offset.reset', $autoOffset);
$conf->set('log_level', LOG_DEBUG);
$consumer = new RdKafka\KafkaConsumer($conf);
$consumer->subscribe([$settlementTopic]);
while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            var_dump(substr($message->payload, strpos($message->payload, '{')));
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            break;
    }
}

