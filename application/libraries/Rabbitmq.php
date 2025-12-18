<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
// application/libraries/Rabbitmq.php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rabbitmq
{
    private $connection;
    private $channel;
    private $queue;

    public function __construct($params = [])
    {
        $CI =& get_instance();
        $CI->load->config('rabbitmq');

        $host     = $CI->config->item('rabbitmq_host');
        $port     = $CI->config->item('rabbitmq_port');
        $user     = $CI->config->item('rabbitmq_user');
        $pass     = $CI->config->item('rabbitmq_pass');
        $this->queue = $CI->config->item('rabbitmq_queue');

        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queue, false, true, false, false);
    }

    public function publish($data)
    {
		log_message('debug', 'RabbitMQ publish - Queue: ' . $this->queue);
		log_message('debug', 'RabbitMQ publish - Data: ' . json_encode($data));
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $publichnya = $this->channel->basic_publish($msg, '', $this->queue);
		log_message('debug', 'RabbitMQ publish - Message sent to ' . $this->queue);
		
		return $publichnya;
    }

    public function consume($callback)
    {
        $this->channel->basic_consume($this->queue, '', false, true, false, false, function ($msg) use ($callback) {
            call_user_func($callback, json_decode($msg->body, true));
        });

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}