<?php

$host = '46.4.162.61';
$port = 443;

$listen_port = getenv('LISTEN_PORT') ?: $port;

echo "relay TCP/IP connections on :${listen_port} to ${host}:${port}\n";

$server = stream_socket_server("tcp://localhost:${listen_port}", $errno, $errstr);

if (!$server) {
    echo "failed to create socket: ${errstr}\n";
    exit(1);
}

while (true) {
    $client = stream_socket_accept($server);
    if (!$client) {
        echo "failed to accept connection\n";
        continue;
    }

    $remote = stream_socket_client("tcp://${host}:${port}", $errno, $errstr, 10);
    if (!$remote) {
        echo "failed to connect to remote host: ${errstr}\n";
        fclose($client);
        continue;
    }

    stream_copy_to_stream($client, $remote);
    stream_copy_to_stream($remote, $client);

    fclose($client);
    fclose($remote);
}
