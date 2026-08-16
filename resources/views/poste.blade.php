@extends('layout')

@section('title', $ref.' — Poste 14')

@push('styles')
<style>
  .bande{
    background:var(--papier);color:var(--encre);border-radius:2px;
    padding:12px 12px 14px;position:relative;overflow:hidden;
    box-shadow:0 6px 0 rgba(0,0,0,.35), inset 0 0 0 1px rgba(0,0,0,.18);
  }
  .bande::before,.bande::after{
    content:"";position:absolute;top:0;bottom:0;width:11px;
    background-image:radial-gradient(circle at 5.5px 6px,var(--bakelite) 2.4px,transparent 2.6px);
    background-size:11px 12px;
  }
  .bande::before{left:0}.bande::after{right:0}
  .bande .inner{padding:0 14px}
  .label{font-size:9px;letter-spacing:.24em;text-transform:uppercase;color:#8A8069;margin-bottom:4px}
  .chiffre{
    font-family:"Courier New",monospace;font-size:12px;letter-spacing:.14em;
    color:#9A9078;word-break:break-word;line-height:1.7;margin-bottom:12px;
  }
  .clair{
    font-family:"Courier New",monospace;font-size:16px;font-weight:700;
    letter-spacing:.1em;line-height:1.75;word-break:break-word;min-height:3.5em;
  }
  .clair b{background:rgba(196,146,46,.32);font-weight:700}

  /* Origine historique, révélée à la résolution. */
  .origine{
    background:var(--panneau);border:1px solid var(--acier);border-left:3px solid var(--laiton);
    border-radius:4px;padding:10px 12px;margin-top:12px;font-size:11px;line-height:1.55;color:#C9CBBF;
    animation:origineIn .35s ease-out;
  }
  .origine[hidden]{display:none}
  .origine b{display:block;color:var(--laiton);font-size:9px;letter-spacing:.2em;text-transform:uppercase;margin-bottom:4px}
  @keyframes origineIn{0%{opacity:0;transform:translateY(6px)}100%{opacity:1;transform:translateY(0)}}

  .tampon{
    position:absolute;right:16px;bottom:8px;border:3px solid var(--signal);
    color:var(--signal);font-size:15px;letter-spacing:.2em;padding:4px 10px;
    transform:rotate(-9deg) scale(2.4);opacity:0;transition:transform .28s cubic-bezier(.2,1.3,.4,1),opacity .18s;
    font-weight:700;pointer-events:none;
  }
  .gagne .tampon{opacity:.85;transform:rotate(-9deg) scale(1)}
  /* Éclat à la victoire : anneau doré permanent + pulsation ponctuelle. */
  .bande.gagne{box-shadow:0 0 0 2px var(--laiton), 0 6px 0 rgba(0,0,0,.35), inset 0 0 0 1px rgba(0,0,0,.18)}
  @keyframes eclat{
    0%{box-shadow:0 0 0 0 rgba(196,146,46,.7), 0 6px 0 rgba(0,0,0,.35), inset 0 0 0 1px rgba(0,0,0,.18)}
    100%{box-shadow:0 0 0 18px rgba(196,146,46,0), 0 6px 0 rgba(0,0,0,.35), inset 0 0 0 1px rgba(0,0,0,.18)}
  }
  .bande.eclat{animation:eclat .6s ease-out}

  /* 3 rotors par ligne maximum, pour la lisibilité : 4 passent sur 3+1, 6 sur 3+3. */
  .rotors{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:20px}
  .rotors.verrouille{opacity:.75}
  .rotor{
    background:linear-gradient(180deg,var(--panneau),#2A2D26);
    border:1px solid var(--acier);border-radius:5px;padding:8px 6px;text-align:center;
  }
  .rotor .nom{font-size:9px;letter-spacing:.2em;color:var(--laiton);margin-bottom:6px}
  .tambour{
    background:#15170F;border:1px solid #0B0C08;border-radius:3px;padding:6px 0;overflow:hidden;
    box-shadow:inset 0 6px 10px rgba(0,0,0,.7);
  }
  .lettre{font-family:"Courier New",monospace;font-size:30px;font-weight:700;color:#E5D9B4;line-height:1}
  /* Petit « flick » mécanique à chaque changement de lettre. */
  @keyframes flick{0%{transform:translateY(-45%);opacity:.25}100%{transform:translateY(0);opacity:1}}
  .lettre.flick{animation:flick .16s ease-out}
  .num{font-size:9px;color:#7E8371;letter-spacing:.12em;margin-top:2px}
  .cran{display:flex;gap:6px;margin-top:8px}
  .cran button{
    flex:1;background:var(--acier);border:none;color:var(--papier);font-size:16px;
    padding:9px 0;border-radius:3px;cursor:pointer;border-bottom:2px solid #3B4036;
    touch-action:manipulation;user-select:none;-webkit-user-select:none;
  }
  .cran button:active{transform:translateY(1px);border-bottom-width:1px}
  .cran button:focus-visible{outline:2px solid var(--laiton);outline-offset:2px}
  .cran button:disabled{opacity:.5;cursor:default}

  .bas{margin-top:18px;display:flex;gap:8px;align-items:center}
  .indice{
    flex:1;background:none;border:1px dashed var(--acier);color:#9BA08D;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;padding:10px;border-radius:3px;cursor:pointer;
  }
  .note{font-size:11px;color:#9BA08D;line-height:1.6;margin-top:12px;min-height:2.4em}
  .note em{color:var(--laiton);font-style:normal}

  @keyframes ctaIn{0%{opacity:0;transform:translateY(6px)}100%{opacity:1;transform:translateY(0)}}
  .cta{
    display:block;margin-top:6px;text-align:center;background:var(--laiton);color:var(--bakelite);
    font-weight:700;font-size:11px;letter-spacing:.16em;text-transform:uppercase;padding:12px;border-radius:4px;
    animation:ctaIn .3s ease-out;
  }
  .cta[hidden]{display:none}

  @media (min-width:640px){
    .clair{font-size:18px}
    .lettre{font-size:34px}
  }
  @media (prefers-reduced-motion:reduce){
    .tampon{transition:none}
    .lettre.flick,.bande.eclat,.cta,.origine{animation:none}
  }
</style>
@endpush

@section('content')
<header>
  <div class="eyebrow"><a href="/">‹ Interceptions</a></div>
  <h1>{{ $ref }}</h1>
  <div class="meta">{{ $rotors }} rotors · réglage inconnu · une seule bande</div>
</header>

<div class="bande" id="bande">
  <div class="inner">
    <div class="label">Signal capté</div>
    <div class="chiffre" id="chiffre">{{ $cipher }}</div>
    <div class="label">Sortie déchiffrée</div>
    <div class="clair" id="clair"></div>
  </div>
  <div class="tampon">EN CLAIR</div>
</div>

<div class="origine" id="origine" hidden><b>Origine du message</b><span id="origine-txt"></span></div>

<div class="rotors" id="rotors"></div>

@if ($motDonne || $indiceRotor)
<div class="bas">
  <button class="indice" id="indice">Demander un indice</button>
</div>
@endif
<div class="note" id="note"></div>
<a class="cta" id="cta" href="/" hidden>Retour aux interceptions ›</a>
@endsection

@push('scripts')
<script>
const A = 65;
// Le chiffré vient du serveur ; le clair et la clé n'y sont jamais.
const CIPHER = @json($cipher);
const PLAIN_HASH = @json($plainHash);
const INDEX = @json($index);
const ROTORS = @json($rotors);
const MOT = @json($motCle);            // '' quand le mot n'est pas donné
const MOT_DONNE = @json($motDonne);
const INDICE_ROTOR = @json($indiceRotor);
const MOT_RE = MOT ? new RegExp(MOT, "g") : null;

const ROMAINS = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII"];
const NOMS = Array.from({length: ROTORS}, (_, i) => "Rotor " + ROMAINS[i]);
let pos = new Array(ROTORS).fill(0);
let prev = new Array(ROTORS).fill(null); // n'anime que les tambours qui changent
let indices = 0;
let resolu = false;
let verrouille = false;
let rotorActif = null;
let silenceProchainGain = false;

function transforme(txt, cle, sens){
  let i = 0;
  return txt.replace(/[A-Z]/g, c => {
    const d = cle[i % cle.length] * sens;
    i++;
    return String.fromCharCode(((c.charCodeAt(0) - A + d) % 26 + 26) % 26 + A);
  });
}

async function sha256(str){
  const buf = await crypto.subtle.digest("SHA-256", new TextEncoder().encode(str));
  return [...new Uint8Array(buf)].map(b => b.toString(16).padStart(2, "0")).join("");
}

// Persistance : le message (pour l'accueil) et le réglage gagnant (pour rouvrir résolu).
function enregistre(message){
  const store = JSON.parse(localStorage.getItem("poste14:decouvertes") || "{}");
  store[INDEX] = message;
  localStorage.setItem("poste14:decouvertes", JSON.stringify(store));
  localStorage.setItem(`poste14:pos:${INDEX}`, JSON.stringify(pos));
}

const rotors = document.getElementById("rotors");
NOMS.forEach((nom, i) => {
  const el = document.createElement("div");
  el.className = "rotor";
  el.innerHTML = `
    <div class="nom">${nom}</div>
    <div class="tambour"><div class="lettre" id="l${i}">A</div><div class="num" id="n${i}">00</div></div>
    <div class="cran">
      <button aria-label="${nom} moins">−</button>
      <button aria-label="${nom} plus">+</button>
    </div>`;
  const [moins, plus] = el.querySelectorAll("button");
  maintien(moins, () => tourne(i, -1));
  maintien(plus, () => tourne(i, 1));
  [moins, plus].forEach(b => b.addEventListener("focus", () => rotorActif = i));
  rotors.appendChild(el);
});

// Appui long = rotation continue ; Entrée/Espace = un cran (clavier).
function maintien(btn, action){
  let t, iv;
  const stop = () => { clearTimeout(t); clearInterval(iv); };
  btn.addEventListener("pointerdown", e => {
    e.preventDefault();
    action();
    t = setTimeout(() => { iv = setInterval(action, 80); }, 300);
  });
  ["pointerup", "pointerleave", "pointercancel"].forEach(ev => btn.addEventListener(ev, stop));
  btn.addEventListener("keydown", e => {
    if (e.key === "Enter" || e.key === " "){ e.preventDefault(); action(); }
  });
}

// Flèches clavier ↑/↓ sur le dernier rotor ciblé.
document.addEventListener("keydown", e => {
  if (rotorActif === null) return;
  if (e.key === "ArrowUp"){ e.preventDefault(); tourne(rotorActif, 1); }
  else if (e.key === "ArrowDown"){ e.preventDefault(); tourne(rotorActif, -1); }
});

function tourne(i, d){
  if (verrouille) return;
  pos[i] = (pos[i] + d + 26) % 26;
  rendu();
}

function flick(i){
  const el = document.getElementById("l" + i);
  el.classList.remove("flick");
  void el.offsetWidth; // relance l'animation
  el.classList.add("flick");
}

let seq = 0;
function rendu(){
  // Déchiffrement local : instantané, aucune requête pendant qu'on tourne.
  pos.forEach((p, i) => {
    document.getElementById("l" + i).textContent = String.fromCharCode(A + p);
    document.getElementById("n" + i).textContent = String(p).padStart(2, "0");
    if (prev[i] !== null && prev[i] !== p) flick(i);
    prev[i] = p;
  });
  const sortie = transforme(CIPHER, pos, -1);
  const clair = document.getElementById("clair");
  if (MOT_DONNE && MOT_RE) clair.innerHTML = sortie.replace(MOT_RE, m => `<b>${m}</b>`);
  else clair.textContent = sortie;

  // Victoire détectée en local via l'empreinte, sans exposer le clair.
  const mine = ++seq;
  sha256(sortie).then(h => {
    if (mine !== seq) return;
    if (h === PLAIN_HASH){ gagner(sortie, silenceProchainGain); silenceProchainGain = false; }
  });
}

function gagner(sortie, silencieux){
  if (resolu) return;
  resolu = true;
  verrouille = true;

  const bande = document.getElementById("bande");
  bande.classList.add("gagne");
  rotors.classList.add("verrouille");
  rotors.querySelectorAll(".cran button").forEach(b => b.disabled = true);
  const b = document.getElementById("indice");
  if (b){ b.disabled = true; b.style.opacity = .4; }
  document.getElementById("cta").hidden = false;

  enregistre(sortie);

  // Origine du message : le serveur ne la livre que sur preuve du réglage gagnant.
  fetch(`/interception/${INDEX}/origine?cle=${pos.join(",")}`).then(r => r.ok ? r.json() : null).then(d => {
    if (!d || !d.sens) return;
    document.getElementById("origine-txt").textContent = d.sens;
    document.getElementById("origine").hidden = false;
  });

  if (!silencieux){
    bande.classList.add("eclat");
    localStorage.setItem("poste14:dernier", String(INDEX));
    if (navigator.vibrate) navigator.vibrate([15, 40, 70]);
  }
}

const btnIndice = document.getElementById("indice");
if (btnIndice){
  const etapes = [];
  if (MOT_DONNE) etapes.push("mot");
  if (INDICE_ROTOR) etapes.push("rotor");

  btnIndice.onclick = function(){
    const note = document.getElementById("note");
    const etape = etapes[indices];
    indices++;

    if (etape === "mot"){
      note.innerHTML = `<em>Indice.</em> Le mot ${MOT} apparaît dans le message. Il est surligné dès qu'il sort.`;
    } else if (etape === "rotor"){
      fetch(`/interception/${INDEX}/indice`).then(r => r.json()).then(d => {
        pos[d.index] = d.pos;
        note.innerHTML = `<em>Indice.</em> Le rotor I est calé sur ${String.fromCharCode(A + d.pos)}. Les autres restent à trouver.`;
        rendu();
      });
    }

    if (indices >= etapes.length){
      this.disabled = true; this.style.opacity = .4;
    }
  };
}

// Rouvrir une interception déjà résolue : on restaure le réglage gagnant, en silence.
const sauve = localStorage.getItem(`poste14:pos:${INDEX}`);
if (sauve){
  try {
    const p = JSON.parse(sauve);
    if (Array.isArray(p) && p.length === ROTORS){ pos = p; silenceProchainGain = true; }
  } catch (e) { /* réglage corrompu : on repart de zéro */ }
}

rendu();
</script>
@endpush
