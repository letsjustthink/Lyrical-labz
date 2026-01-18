<?php
/* FILE: post.php */
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Bad JSON"]); exit; }

$room = preg_replace('/[^A-Z0-9]/', '', strtoupper($data['room'] ?? ''));
$clientId = preg_replace('/[^a-zA-Z0-9\-]/', '', $data['clientId'] ?? '');
$name = substr(preg_replace('/[^A-Z0-9 _\-]/', '', strtoupper($data['name'] ?? 'PRODUCER')), 0, 12);
$type = $data['type'] ?? '';

if (strlen($room) !== 6) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Room must be 6 chars"]); exit; }
if (!$clientId) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Missing clientId"]); exit; }

$dir = __DIR__ . "/rooms";
if (!is_dir($dir)) @mkdir($dir, 0755, true);

$path = $dir . "/$room.json";

function loadState($path, $room) {
  if (!file_exists($path)) {
    return [
      "roomId" => $room,
      "players" => [],
      "turnIndex" => 0,
      "currentLine" => [],
      "song" => [],
      "section" => ["type"=>"VERSE","verse"=>1,"bar"=>1,"barsPerSection"=>4],
      "updatedAt" => time()
    ];
  }
  $txt = file_get_contents($path);
  $st = json_decode($txt, true);
  return $st ?: [
    "roomId" => $room,
    "players" => [],
    "turnIndex" => 0,
    "currentLine" => [],
    "song" => [],
    "section" => ["type"=>"VERSE","verse"=>1,"bar"=>1,"barsPerSection"=>4],
    "updatedAt" => time()
  ];
}

function saveStateAtomic($path, $state) {
  $tmp = $path . ".tmp";
  file_put_contents($tmp, json_encode($state, JSON_UNESCAPED_SLASHES));
  rename($tmp, $path);
}

$fp = fopen($path, 'c+');
if (!$fp) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"Cannot open room file"]); exit; }

flock($fp, LOCK_EX);

$state = loadState($path, $room);
$players = $state["players"] ?? [];
$turnIndex = intval($state["turnIndex"] ?? 0);

$findPlayerIndex = function($players, $clientId) {
  foreach ($players as $i => $p) if (($p["id"] ?? "") === $clientId) return $i;
  return -1;
};

$idx = $findPlayerIndex($players, $clientId);

if ($type === "join") {
  if ($idx === -1) {
    $players[] = ["id"=>$clientId, "name"=>$name];
    if (count($players) === 1) $turnIndex = 0;
  } else {
    // update display name (cosmetic)
    $players[$idx]["name"] = $name;
  }
  $state["players"] = $players;
  $state["turnIndex"] = $turnIndex;
}
elseif ($type === "word") {
  $word = strtoupper(trim($data["word"] ?? ""));
  $word = preg_split('/\s+/', $word)[0] ?? "";
  if ($word === "") { flock($fp, LOCK_UN); fclose($fp); echo json_encode(["ok"=>false,"error"=>"Empty word"]); exit; }

  if ($idx === -1) { flock($fp, LOCK_UN); fclose($fp); echo json_encode(["ok"=>false,"error"=>"Join first"]); exit; }

  $currentPlayer = $players[$turnIndex] ?? null;
  if (!$currentPlayer || ($currentPlayer["id"] ?? "") !== $clientId) {
    flock($fp, LOCK_UN); fclose($fp); echo json_encode(["ok"=>false,"error"=>"Not your turn"]); exit;
  }

  $state["currentLine"][] = $word;
  // advance turn
  $turnIndex = ($turnIndex + 1) % max(1, count($players));
  $state["turnIndex"] = $turnIndex;
}
elseif ($type === "nextBar") {
  $sec = $state["section"] ?? ["type"=>"VERSE","verse"=>1,"bar"=>1,"barsPerSection"=>4];

  $text = trim(implode(" ", $state["currentLine"] ?? []));
  if ($text !== "") {
    $state["song"][] = [
      "section" => ($sec["type"]." ".$sec["verse"]),
      "bar" => intval($sec["bar"]),
      "text" => $text
    ];
  }
  $state["currentLine"] = [];

  // advance bar/section: Verse(4) -> Chorus(4) -> Verse(4)...
  $bar = intval($sec["bar"]) + 1;
  $barsPer = intval($sec["barsPerSection"]);

  if ($bar > $barsPer) {
    $bar = 1;
    if ($sec["type"] === "VERSE") {
      $sec["type"] = "CHORUS";
    } else {
      $sec["type"] = "VERSE";
      $sec["verse"] = intval($sec["verse"]) + 1;
    }
  }

  $sec["bar"] = $bar;
  $sec["barsPerSection"] = 4;
  $state["section"] = $sec;
}
else {
  flock($fp, LOCK_UN); fclose($fp);
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"Unknown type"]);
  exit;
}

$state["updatedAt"] = time();

// write updated state
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($state, JSON_UNESCAPED_SLASHES));

flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(["ok"=>true]);
