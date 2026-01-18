<?php
/* FILE: stream.php */
set_time_limit(0);

$room = preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['room'] ?? ''));
$clientId = preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['clientId'] ?? '');
$name = substr(preg_replace('/[^A-Z0-9 _\-]/', '', strtoupper($_GET['name'] ?? 'PRODUCER')), 0, 12);

if (strlen($room) !== 6) { http_response_code(400); echo "bad room"; exit; }

$dir = __DIR__ . "/rooms";
if (!is_dir($dir)) @mkdir($dir, 0755, true);
$path = $dir . "/$room.json";

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header("X-Accel-Buffering: no"); // helps some proxies

function sse($event, $data) {
  echo "event: $event\n";
  echo "data: " . $data . "\n\n";
  @ob_flush();
  @flush();
}

sse("hello", json_encode(["ok"=>true,"room"=>$room]));

// stream loop: only sends when state file changes
$lastHash = "";
while (true) {
  clearstatcache(true, $path);

  if (file_exists($path)) {
    $txt = @file_get_contents($path);
    if ($txt !== false) {
      $hash = sha1($txt);
      if ($hash !== $lastHash) {
        $lastHash = $hash;
        sse("state", $txt);
      }
    }
  } else {
    // if room file not created yet, send empty starter state once
    $starter = json_encode([
      "roomId"=>$room,
      "players"=>[],
      "turnIndex"=>0,
      "currentLine"=>[],
      "song"=>[],
      "section"=>["type"=>"VERSE","verse"=>1,"bar"=>1,"barsPerSection"=>4],
      "updatedAt"=>time()
    ]);
    $hash = sha1($starter);
    if ($hash !== $lastHash) {
      $lastHash = $hash;
      sse("state", $starter);
    }
  }

  // keep-alive ping (some hosts kill idle streams)
  sse("ping", json_encode(["t"=>time()]));

  // 0.5s tick; NOT polling from clients, server holds one stream per client
  usleep(500000);
}
