<?php

namespace App\Http\Controllers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Students;
use App\Http\Controllers\Api\StatsController;

class WebSocketController implements MessageComponentInterface
{
    protected $clients;
    
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        
        // Send initial data to the new client
        $this->sendStudentCount($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        // Parse the received message
        $data = json_decode($msg, true);
        
        // Handle different message types
        if (isset($data['action']) && $data['action'] === 'get_student_count') {
            $this->sendStudentCount($from);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // Helper method to send student count
    protected function sendStudentCount(ConnectionInterface $conn)
    {
        $studentCount = Students::count();
        $conn->send(json_encode([
            'type' => 'student_count',
            'value' => $studentCount
        ]));
    }
    
    // Broadcast tracer stats to all connected clients
    public function broadcastTracerStats()
    {
        $statsController = new StatsController();
        $response = $statsController->getTracerStats()->getContent();
        $data = json_decode($response, true);
        
        if (isset($data['status']) && $data['status'] === 'success') {
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'type' => 'tracer_data',
                    'value' => $data['data']
                ]));
            }
        }
    }
}
