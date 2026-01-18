/* FILE: client.js */
(() => {
  const $ = (id) => document.getElementById(id);

  const roomPill = $("roomPill");
  const sectionPill = $("sectionPill");
  const turnPill = $("turnPill");
  const teleprompter = $("teleprompter");
  const currentLineEl = $("currentLine");

  const nameInput = $("name");
  const roomInput = $("room");
  const wordInput = $("word");

  const joinBtn = $("joinBtn");
  const sendBtn = $("sendBtn");
  const nextBarBtn = $("nextBarBtn");

  let room = "";
  let name = "";
  let clientId = "";
  let evtSource = null;

  function setStatus(text, isYourTurn = false) {
    turnPill.textContent = text;
    turnPill.style.opacity = "1";
    turnPill.className = isYourTurn ? "pill yellow" : "pill";
  }

  function renderState(state) {
    // state: { players, turnIndex, currentLine, song, section }
    roomPill.textContent = `ROOM: ${state.roomId || room}`;
    const sec = state.section || { type: "VERSE", verse: 1, bar: 1, barsPerSection: 4 };
    sectionPill.textContent = `${sec.type} ${sec.verse} • BAR ${sec.bar}/${sec.barsPerSection}`;

    // Teleprompter = committed bars
    teleprompter.innerHTML = "";
    (state.song || []).forEach((l) => {
      const div = document.createElement("div");
      div.className = "line";
      div.innerHTML = `<div class="muted">${l.section} • BAR ${l.bar}</div><div style="font-weight:900; font-size:18px;">${escapeHtml(l.text)}</div>`;
      teleprompter.appendChild(div);
    });

    // Current line being built
    const current = (state.currentLine || []).join(" ");
    currentLineEl.innerHTML = `<div class="muted">Current line:</div><div style="font-weight:900; font-size:20px; letter-spacing:.02em;">${escapeHtml(current || "…")}</div>`;

    // Turn prompt
    const players = state.players || [];
    const turnIndex = state.turnIndex ?? 0;
    const currentPlayer = players[turnIndex];
    if (!currentPlayer) {
      setStatus("WAITING FOR PLAYERS…", false);
      return;
    }

    const isMe = currentPlayer.id === clientId;
    setStatus(isMe ? "NEXT WORD: IT'S YOUR TURN" : `WAITING: ${currentPlayer.name}`, isMe);

    // Enable/disable inputs
    wordInput.disabled = !isMe;
    sendBtn.disabled = !isMe;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (c) => ({
      "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"
    }[c]));
  }

  async function postAction(type, payload = {}) {
    const res = await fetch("./post.php", {
      method: "POST",
      headers: { "Content-Type":"application/json" },
      body: JSON.stringify({ room, clientId, name, type, ...payload })
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data?.ok === false) {
      alert(data?.error || "Request failed");
    }
  }

  function connectStream() {
    if (evtSource) evtSource.close();
    setStatus("CONNECTING…", false);

    // SSE stream holds open and server pushes updates
    evtSource = new EventSource(`./stream.php?room=${encodeURIComponent(room)}&clientId=${encodeURIComponent(clientId)}&name=${encodeURIComponent(name)}`);

    evtSource.addEventListener("state", (e) => {
      try {
        const state = JSON.parse(e.data);
        renderState(state);
      } catch {}
    });

    evtSource.addEventListener("hello", (e) => {
      try {
        const msg = JSON.parse(e.data);
        setStatus("CONNECTED", false);
      } catch {}
    });

    evtSource.onerror = () => {
      setStatus("DISCONNECTED (RETRYING…)", false);
      // browser auto-retries EventSource
    };
  }

  joinBtn.onclick = async () => {
    name = (nameInput.value || "PRODUCER").trim().toUpperCase();
    room = (roomInput.value || "").trim().toUpperCase();
    if (room.length !== 6) return alert("Room code must be 6 chars (e.g., ABC123)");

    // clientId is session identity (not “real auth”)
    clientId = crypto?.randomUUID?.() || String(Math.random()).slice(2);

    connectStream();

    // register/join room
    await postAction("join");
  };

  sendBtn.onclick = async () => {
    const word = (wordInput.value || "").trim().split(/\s+/)[0];
    if (!word) return;
    wordInput.value = "";
    await postAction("word", { word });
  };

  nextBarBtn.onclick = async () => {
    await postAction("nextBar");
  };

  wordInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendBtn.onclick();
  });

})();
