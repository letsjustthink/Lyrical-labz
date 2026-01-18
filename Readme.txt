folder structure rreadme 
prepare these folders in this structure
then create these files. with in the folders
this ***UODATED CODE*** replaces  


/lyrical-lab/
  index.html
  client.js
  post.php
  stream.php
  /rooms/   (folder, writable)


***version 2 ***
Changes folder structure
lyrical-lab/
  index.html
  .gitignore
  js/
    utils.js
    main.js
  api/
    _util.php
    bootstrap.php
    state.php
    heartbeat.php
    submit_word.php
    end_line.php
    set_section.php
    tick.php
  data/
    .htaccess
    MainStudio01.json
    hof_roster.json

**index.html**
<!-- SAVE AS: index.html -->
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>LYRICAL LAB | MainStudio01</title>

  <!-- If your site injects a <base>, Fix B still works because JS resolves via import.meta.url -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body{background:#020208;color:#f0faff;margin:0;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial}
    .card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);backdrop-filter: blur(16px)}
    .dim{opacity:.25;filter:saturate(.2)}
  </style>
</head>
<body>
  <div class="min-h-screen p-4 md:p-8">
    <div class="max-w-5xl mx-auto space-y-6">
      <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
          <div class="text-[10px] uppercase tracking-[0.5em] text-cyan-500/50 font-black">Neural Production Studio</div>
          <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white">LYRICAL <span class="text-cyan-400">LAB</span></h1>
          <div class="mt-2 text-[11px] text-cyan-500/50 font-black uppercase tracking-[0.35em]">MainStudio01 • no-build • php/file realtime</div>
        </div>
        <div class="card rounded-2xl p-4">
          <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Your Status</div>
          <div id="statusLine" class="mt-2 text-sm text-cyan-200 font-mono">Booting…</div>
        </div>
      </header>

      <!-- NAME GATE -->
      <section id="nameGate" class="card rounded-3xl p-5 md:p-8">
        <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
          <div class="space-y-2">
            <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Welcome</div>
            <div class="text-2xl md:text-3xl font-black text-white">
              <span class="text-cyan-400">DJ</span> <span id="autoDjName" class="font-mono">DX1</span>
            </div>
            <div class="text-[12px] text-white/40 max-w-xl">
              Keep this name (fast) or type your own (creative). A cookie keeps your identity so you can reconnect.
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <button id="keepNameBtn"
              class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-black font-black uppercase tracking-widest text-[11px]">
              OK • Keep
            </button>

            <input id="nameInput" maxlength="16" placeholder="TYPE DJ NAME…"
              class="w-full sm:w-72 px-5 py-3 rounded-2xl bg-black/50 border border-white/10 text-white font-black uppercase tracking-widest outline-none focus:border-cyan-400"
            />
            <button id="useTypedBtn"
              class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/15 text-white border border-white/10 font-black uppercase tracking-widest text-[11px]">
              Use Typed
            </button>
          </div>
        </div>

        <div class="mt-6 grid md:grid-cols-2 gap-4">
          <div class="card rounded-2xl p-4">
            <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">How to play</div>
            <ul class="mt-3 text-sm text-white/60 space-y-2">
              <li>• You add <b>one word</b> on your turn.</li>
              <li>• Pick a word that fits what came before it.</li>
              <li>• If you’re stuck, use hints below (toggle later).</li>
            </ul>
          </div>
          <div class="card rounded-2xl p-4">
            <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Hints (for non-creatives 😈)</div>
            <div class="mt-3 text-sm text-white/60 space-y-2">
              <div><b>Noun</b> = person/place/thing (I, we, you, they)</div>
              <div><b>Verb</b> = action (run, feel, break)</div>
              <div><b>Adjective</b> = describing word (cold, loud, golden)</div>
            </div>
          </div>
        </div>
      </section>

      <!-- MAIN STUDIO -->
      <section id="studio" class="hidden">
        <div class="grid lg:grid-cols-[1fr_360px] gap-6">

          <!-- CENTER / GAME -->
          <div class="card rounded-3xl p-5 md:p-8 space-y-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Now recording</div>
                <div id="sectionLine" class="text-2xl md:text-3xl font-black text-white">VERSE 1</div>
                <div id="barLine" class="text-[12px] text-cyan-400/80 font-mono mt-1">BAR 1</div>
              </div>

              <div class="flex items-center gap-3">
                <div class="card rounded-2xl px-4 py-3">
                  <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Turn Timer</div>
                  <div id="turnTimer" class="text-2xl font-black font-mono text-cyan-300">60</div>
                </div>
                <button id="leaveBtn" class="px-4 py-3 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-black uppercase tracking-widest text-[11px]">
                  Leave
                </button>
              </div>
            </div>

            <div class="card rounded-3xl p-5">
              <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Current Line</div>
              <div id="currentLine" class="mt-3 text-2xl md:text-4xl font-black text-white italic flex flex-wrap gap-x-3 gap-y-2"></div>
              <div id="turnHint" class="mt-4 text-sm text-white/40 font-black uppercase tracking-widest"></div>
            </div>

            <div class="flex flex-col md:flex-row gap-3">
              <input id="wordInput" maxlength="24" placeholder="ADD ONE WORD…"
                class="flex-1 px-5 py-4 rounded-2xl bg-black/50 border border-white/10 text-white font-black uppercase tracking-widest outline-none focus:border-cyan-400"
              />
              <button id="sendWordBtn"
                class="px-6 py-4 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-black font-black uppercase tracking-widest">
                Send
              </button>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
              <button id="endLineBtn" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/15 text-white border border-white/10 font-black uppercase tracking-widest text-[11px]">
                End Line
              </button>

              <button id="nextBarBtn" class="px-5 py-3 rounded-2xl bg-yellow-400 hover:bg-yellow-300 text-black font-black uppercase tracking-widest text-[11px]">
                BAR 1
              </button>

              <button id="chorusBtn" class="px-5 py-3 rounded-2xl bg-purple-500 hover:bg-purple-400 text-white font-black uppercase tracking-widest text-[11px]">
                Chorus
              </button>

              <button id="verseBtn" class="px-5 py-3 rounded-2xl bg-green-600 hover:bg-green-500 text-white font-black uppercase tracking-widest text-[11px]">
                Verse
              </button>
            </div>

            <div class="card rounded-3xl p-5">
              <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Teleprompter</div>
              <div id="teleprompter" class="mt-4 space-y-3 max-h-[320px] overflow-auto pr-2"></div>
            </div>
          </div>

          <!-- RIGHT / HALL OF FAME -->
          <aside class="card rounded-3xl p-5 md:p-6">
            <div class="flex items-end justify-between">
              <div>
                <div class="text-[10px] uppercase tracking-[0.35em] font-black text-white/50">Hall of Fame</div>
                <div class="text-xl font-black text-white">DJs who touched the lab</div>
                <div class="text-[12px] text-white/40 mt-1">Active DJs glow. After 5 mins idle, they dim. Gimmick? Yes. Effective? Also yes.</div>
              </div>
              <div class="text-[11px] text-cyan-300 font-mono font-black" id="visitorCount">…</div>
            </div>

            <div id="hofList" class="mt-5 space-y-2"></div>
          </aside>
        </div>
      </section>
    </div>
  </div>

  <script type="module" src="./js/main.js"></script>
</body>
</html>

☆☆/* SAVE AS: js/utils.js */

/*
  Fix B: Path resolution anchored to this JS file (import.meta.url)
  immune to <base href> and folder depth.
*/

const SCRIPT_BASE = import.meta.url;

export function projectRootUrl() {
  // /js/utils.js -> go up to project root (folder containing index.html)
  return new URL("..", SCRIPT_BASE);
}

export function apiUrl(path) {
  const root = projectRootUrl();
  return new URL(path.replace(/^\/+/, ""), root).toString();
}

export async function getJSON(path) {
  const res = await fetch(apiUrl(path), { cache: "no-store" });
  return res.json();
}

export async function postJSON(path, body = {}) {
  const res = await fetch(apiUrl(path), {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  });
  return res.json();
}

export function setCookie(name, value, days = 365) {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

export function getCookie(name) {
  const key = encodeURIComponent(name) + "=";
  const parts = document.cookie.split(";").map(s => s.trim());
  for (const p of parts) if (p.startsWith(key)) return decodeURIComponent(p.slice(key.length));
  return "";
}

export function clampStr(s, max) {
  return (s || "").toString().slice(0, max);
}

export function sanitizeDjName(s, max = 16) {
  s = clampStr(s, max).toUpperCase();
  // allow A-Z 0-9 space _ -
  s = s.replace(/[^A-Z0-9 _-]/g, "");
  // collapse whitespace
  s = s.replace(/\s+/g, " ").trim();
  return s;
}

☆end of file start new file☆☆
/* SAVE AS: js/main.js */
import { getJSON, postJSON, setCookie, getCookie, sanitizeDjName } from "./utils.js";

const IDLE_DIM_SECONDS = 300;     // 5 minutes
const POLL_MS = 1500;             // "real-time-ish" without melting your server
const HEARTBEAT_MS = 10000;       // keeps you marked "active"
const TURN_SECONDS = 60;

const $ = (id) => document.getElementById(id);

const els = {
  statusLine: $("statusLine"),
  nameGate: $("nameGate"),
  studio: $("studio"),
  autoDjName: $("autoDjName"),
  keepNameBtn: $("keepNameBtn"),
  nameInput: $("nameInput"),
  useTypedBtn: $("useTypedBtn"),

  sectionLine: $("sectionLine"),
  barLine: $("barLine"),
  nextBarBtn: $("nextBarBtn"),

  currentLine: $("currentLine"),
  wordInput: $("wordInput"),
  sendWordBtn: $("sendWordBtn"),
  endLineBtn: $("endLineBtn"),
  chorusBtn: $("chorusBtn"),
  verseBtn: $("verseBtn"),
  teleprompter: $("teleprompter"),
  turnHint: $("turnHint"),

  turnTimer: $("turnTimer"),
  leaveBtn: $("leaveBtn"),

  hofList: $("hofList"),
  visitorCount: $("visitorCount"),
};

let state = null;
let me = { visitorId: "", djName: "" };
let pollTimer = null;
let hbTimer = null;
let localTickTimer = null;

function setStatus(t) {
  els.statusLine.textContent = t;
}

function showStudio() {
  els.nameGate.classList.add("hidden");
  els.studio.classList.remove("hidden");
}

function showGate() {
  els.nameGate.classList.remove("hidden");
  els.studio.classList.add("hidden");
}

function renderTeleprompter(songLines = []) {
  els.teleprompter.innerHTML = "";
  let lastSec = "";
  for (const line of songLines) {
    if (line.section !== lastSec) {
      const h = document.createElement("div");
      h.className = "text-[10px] uppercase tracking-[0.35em] font-black text-cyan-500/60 pt-2 border-t border-white/5 first:border-0";
      h.textContent = `[${line.section}]`;
      els.teleprompter.appendChild(h);
      lastSec = line.section;
    }
    const p = document.createElement("div");
    p.className = "text-lg md:text-xl text-white font-black italic";
    p.textContent = line.text;
    els.teleprompter.appendChild(p);
  }
}

function renderCurrentLine(words = []) {
  els.currentLine.innerHTML = "";
  if (!words.length) {
    const s = document.createElement("span");
    s.className = "text-white/10 uppercase tracking-widest text-sm font-black";
    s.textContent = "Awaiting word…";
    els.currentLine.appendChild(s);
    return;
  }
  for (const w of words) {
    const sp = document.createElement("span");
    sp.className = "text-white";
    sp.textContent = w.text;
    els.currentLine.appendChild(sp);
  }
}

function renderHallOfFame(hof) {
  const players = hof?.players || {};
  const now = Math.floor(Date.now() / 1000);
  const entries = Object.entries(players)
    .map(([id, p]) => ({ id, ...p }))
    .sort((a,b) => (b.lastSeen||0) - (a.lastSeen||0))
    .slice(0, 40);

  els.visitorCount.textContent = `TOTAL: ${Object.keys(players).length}`;

  els.hofList.innerHTML = "";
  for (const e of entries) {
    const idle = now - (e.lastSeen || 0);
    const isDim = idle > IDLE_DIM_SECONDS;

    const row = document.createElement("div");
    row.className = `flex items-center justify-between px-4 py-3 rounded-2xl border border-white/5 bg-white/5 ${isDim ? "dim" : ""}`;

    const left = document.createElement("div");
    left.className = "font-black uppercase tracking-widest text-[11px] text-white";
    left.textContent = e.djName || "DJ";

    const right = document.createElement("div");
    right.className = "text-[10px] font-mono font-black text-cyan-300/70";
    right.textContent = idle < 10 ? "LIVE" : `${idle}s`;

    row.appendChild(left);
    row.appendChild(right);
    els.hofList.appendChild(row);
  }
}

function computeTimer() {
  if (!state) return;
  const now = Math.floor(Date.now()/1000);
  const started = state.turnStartedAt || now;
  const elapsed = Math.max(0, now - started);
  const remaining = Math.max(0, TURN_SECONDS - elapsed);
  els.turnTimer.textContent = String(remaining);

  const myTurn = state.turnUid === me.visitorId;
  const turnName = state.players?.[state.turnUid]?.djName || "DJ";
  els.turnHint.textContent = myTurn
    ? "NEXT WORD • IT'S YOUR TURN"
    : `WAITING • ${turnName} IS UP`;

  // nice UX: disable input if not your turn
  els.wordInput.disabled = !myTurn;
  els.sendWordBtn.disabled = !myTurn;
  els.wordInput.classList.toggle("opacity-50", !myTurn);
  els.sendWordBtn.classList.toggle("opacity-50", !myTurn);
}

function renderState() {
  if (!state) return;
  els.sectionLine.textContent = state.currentSection || "VERSE 1";
  els.barLine.textContent = `BAR ${state.bar || 1}`;
  els.nextBarBtn.textContent = `BAR ${state.bar || 1}`;

  renderCurrentLine(state.currentLine || []);
  renderTeleprompter(state.songLines || []);
  computeTimer();
}

async function poll() {
  try {
    // server-side tick moves turns when timer expires
    await postJSON("api/tick.php", {});
    const res = await getJSON("api/state.php");
    state = res?.room || null;
    renderState();
    renderHallOfFame(res?.hof || {});
    setStatus(`Connected • ${me.djName} • ${me.visitorId}`);
  } catch (e) {
    setStatus("Disconnected • retrying…");
  }
}

async function heartbeat() {
  try { await postJSON("api/heartbeat.php", {}); } catch {}
}

async function submitWord() {
  const raw = els.wordInput.value || "";
  const word = sanitizeDjName(raw, 24).split(" ")[0]; // one word
  if (!word) return;

  els.wordInput.value = "";
  await postJSON("api/submit_word.php", { word });
  await poll();
}

async function endLine() {
  await postJSON("api/end_line.php", {});
  await poll();
}

async function setSection(section) {
  await postJSON("api/set_section.php", { section });
  await poll();
}

async function nextBar() {
  await postJSON("api/set_section.php", { barIncrement: 1 });
  await poll();
}

async function bootstrap() {
  const boot = await postJSON("api/bootstrap.php", {});
  me.visitorId = boot.visitorId;
  me.djName = boot.djName;
  els.autoDjName.textContent = me.djName.replace(/^DJ\s*/,"");
  setStatus(`Ready • ${me.djName}`);

  // show gate unless already accepted previously
  const accepted = getCookie("LL_ACCEPTED") === "1";
  if (accepted) {
    showStudio();
    startLoops();
  } else {
    showGate();
  }
}

function startLoops() {
  clearInterval(pollTimer);
  clearInterval(hbTimer);
  clearInterval(localTickTimer);

  pollTimer = setInterval(poll, POLL_MS);
  hbTimer = setInterval(heartbeat, HEARTBEAT_MS);
  localTickTimer = setInterval(computeTimer, 250);

  poll();
  heartbeat();
  setTimeout(() => els.wordInput?.focus(), 150);
}

function stopLoops() {
  clearInterval(pollTimer);
  clearInterval(hbTimer);
  clearInterval(localTickTimer);
}

els.keepNameBtn.addEventListener("click", async () => {
  setCookie("LL_ACCEPTED", "1");
  showStudio();
  startLoops();
});

els.useTypedBtn.addEventListener("click", async () => {
  const typed = sanitizeDjName(els.nameInput.value, 16);
  if (!typed) return;
  setCookie("LL_ACCEPTED", "1");
  setCookie("LL_DJ_NAME", typed);
  await postJSON("api/bootstrap.php", { djName: typed }); // update on server + cookie
  showStudio();
  startLoops();
});

els.sendWordBtn.addEventListener("click", submitWord);
els.wordInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter") submitWord();
});

els.endLineBtn.addEventListener("click", endLine);
els.chorusBtn.addEventListener("click", () => setSection("CHORUS"));
els.verseBtn.addEventListener("click", () => setSection("VERSE"));
els.nextBarBtn.addEventListener("click", nextBar);

els.leaveBtn.addEventListener("click", () => {
  stopLoops();
  // leaving is just stopping loops; cookie keeps identity for reconnect
  setStatus("Left session (cookie preserved). Refresh to rejoin.");
  showGate();
});

bootstrap();

☆☆
<?php
/* SAVE AS: api/_util.php */

function resp_json($arr, $code=200) {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($arr);
  exit;
}

function read_json($path, $default) {
  if (!file_exists($path)) return $default;
  $raw = @file_get_contents($path);
  if ($raw === false || $raw === "") return $default;
  $d = json_decode($raw, true);
  return is_array($d) ? $d : $default;
}

function write_json_atomic($path, $data) {
  $dir = dirname($path);
  if (!is_dir($dir)) @mkdir($dir, 0775, true);

  $tmp = $path . ".tmp." . bin2hex(random_bytes(4));
  $json = json_encode($data, JSON_UNESCAPED_SLASHES);

  // write temp then rename (atomic on same filesystem)
  $fp = fopen($tmp, "wb");
  if (!$fp) return false;
  fwrite($fp, $json);
  fclose($fp);

  return rename($tmp, $path);
}

function with_lock($lockPath, $fn) {
  $fp = fopen($lockPath, "c+");
  if (!$fp) return $fn();
  flock($fp, LOCK_EX);
  $res = $fn();
  flock($fp, LOCK_UN);
  fclose($fp);
  return $res;
}

function cookie_get($name) {
  return isset($_COOKIE[$name]) ? $_COOKIE[$name] : "";
}

function cookie_set($name, $value, $days=365) {
  setcookie($name, $value, [
    "expires" => time() + $days*86400,
    "path" => "/",
    "secure" => false,  // set true if HTTPS
    "httponly" => false,
    "samesite" => "Lax"
  ]);
}

function sanitize_dj($s, $max=16) {
  $s = strtoupper(substr((string)$s, 0, $max));
  $s = preg_replace("/[^A-Z0-9 _-]/", "", $s);
  $s = preg_replace("/\s+/", " ", $s);
  $s = trim($s);
  return $s;
}

function sanitize_word($s, $max=24) {
  $s = strtoupper(substr((string)$s, 0, $max));
  $s = preg_replace("/[^A-Z0-9_-]/", "", $s);
  return $s;
}

function json_body() {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}

function generate_id($len=12) {
  $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  $out = "";
  for ($i=0; $i<$len; $i++) $out .= $chars[random_int(0, strlen($chars)-1)];
  return $out;
}

function data_paths() {
  $base = __DIR__ . "/../data";
  return [
    "room" => $base . "/MainStudio01.json",
    "hof"  => $base . "/hof_roster.json",
    "lock" => __DIR__ . "/_state.lock"
  ];
}

☆☆
<?php
/* SAVE AS: api/bootstrap.php */
require_once __DIR__ . "/_util.php";

$paths = data_paths();
$body = json_body();

with_lock($paths["lock"], function() use ($paths, $body) {

  $visitorId = cookie_get("LL_VISITOR_ID");
  if (!$visitorId) {
    $visitorId = "V" . generate_id(10);
    cookie_set("LL_VISITOR_ID", $visitorId);
  }

  $dj = cookie_get("LL_DJ_NAME");
  if (isset($body["djName"]) && $body["djName"] !== "") {
    $dj = $body["djName"];
    cookie_set("LL_DJ_NAME", $dj);
  }

  // If no DJ name, auto-generate DJ DX###
  $dj = sanitize_dj($dj, 16);
  if (!$dj) {
    $hof = read_json($paths["hof"], ["seq"=>0, "players"=>[]]);
    $hof["seq"] = isset($hof["seq"]) ? (int)$hof["seq"] : 0;
    $hof["seq"] += 1;
    $dj = "DJ DX" . $hof["seq"];
    cookie_set("LL_DJ_NAME", $dj);
    write_json_atomic($paths["hof"], $hof); // save seq
  }

  // ensure HOF entry
  $hof = read_json($paths["hof"], ["seq"=>0, "players"=>[]]);
  if (!isset($hof["players"][$visitorId])) {
    $hof["players"][$visitorId] = ["djName"=>$dj, "firstSeen"=>time(), "lastSeen"=>time()];
  } else {
    $hof["players"][$visitorId]["djName"] = $dj;
    $hof["players"][$visitorId]["lastSeen"] = time();
  }
  write_json_atomic($paths["hof"], $hof);

  // ensure room exists
  $room = read_json($paths["room"], null);
  if (!$room) {
    $room = [
      "roomId" => "MainStudio01",
      "createdAt" => time(),
      "currentSection" => "VERSE 1",
      "bar" => 1,
      "turnUid" => $visitorId,
      "turnStartedAt" => time(),
      "players" => [],
      "currentLine" => [],
      "songLines" => [],
    ];
  }

  // register player in room
  if (!isset($room["players"][$visitorId])) {
    $room["players"][$visitorId] = ["djName"=>$dj, "lastSeen"=>time()];
    // if no turn set, give it
    if (empty($room["turnUid"])) {
      $room["turnUid"] = $visitorId;
      $room["turnStartedAt"] = time();
    }
  } else {
    $room["players"][$visitorId]["djName"] = $dj;
    $room["players"][$visitorId]["lastSeen"] = time();
  }

  write_json_atomic($paths["room"], $room);

  resp_json(["ok"=>true, "visitorId"=>$visitorId, "djName"=>$dj]);
});

☆☆
<?php
/* SAVE AS: api/state.php */
require_once __DIR__ . "/_util.php";

$paths = data_paths();
$room = read_json($paths["room"], null);
now can $hof  = read_json($paths["hof"], ["players"=>[]]);

resp_json(["ok"=>true, "room"=>$room, "hof"=>$hof]);
