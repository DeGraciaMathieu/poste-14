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

  .tampon{
    position:absolute;right:16px;bottom:8px;border:3px solid var(--signal);
    color:var(--signal);font-size:15px;letter-spacing:.2em;padding:4px 10px;
    transform:rotate(-9deg) scale(2.4);opacity:0;transition:transform .28s cubic-bezier(.2,1.3,.4,1),opacity .18s;
    font-weight:700;pointer-events:none;
  }
  .gagne .tampon{opacity:.85;transform:rotate(-9deg) scale(1)}

  /* 3 rotors par ligne maximum, pour la lisibilité : 4 passent sur 3+1, 6 sur 3+3. */
  .rotors{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:20px}
  .rotor{
    background:linear-gradient(180deg,var(--panneau),#2A2D26);
    border:1px solid var(--acier);border-radius:5px;padding:8px 6px;text-align:center;
  }
  .rotor .nom{font-size:9px;letter-spacing:.2em;color:var(--laiton);margin-bottom:6px}
  .tambour{
    background:#15170F;border:1px solid #0B0C08;border-radius:3px;padding:6px 0;
    box-shadow:inset 0 6px 10px rgba(0,0,0,.7);
  }
  .lettre{font-family:"Courier New",monospace;font-size:30px;font-weight:700;color:#E5D9B4;line-height:1}
  .num{font-size:9px;color:#7E8371;letter-spacing:.12em;margin-top:2px}
  .cran{display:flex;gap:6px;margin-top:8px}
  .cran button{
    flex:1;background:var(--acier);border:none;color:var(--papier);font-size:16px;
    padding:9px 0;border-radius:3px;cursor:pointer;border-bottom:2px solid #3B4036;
  }
  .cran button:active{transform:translateY(1px);border-bottom-width:1px}
  .cran button:focus-visible{outline:2px solid var(--laiton);outline-offset:2px}

  .bas{margin-top:18px;display:flex;gap:8px;align-items:center}
  .indice{
    flex:1;background:none;border:1px dashed var(--acier);color:#9BA08D;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;padding:10px;border-radius:3px;cursor:pointer;
  }
  .note{font-size:11px;color:#9BA08D;line-height:1.6;margin-top:12px;min-height:2.4em}
  .note em{color:var(--laiton);font-style:normal}

  @media (min-width:640px){
    .clair{font-size:18px}
    .lettre{font-size:34px}
  }
  @media (prefers-reduced-motion:reduce){.tampon{transition:none}}
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

<div class="rotors" id="rotors"></div>

@if ($motDonne || $indiceRotor)
<div class="bas">
  <button class="indice" id="indice">Demander un indice</button>
</div>
@endif
<div class="note" id="note">Tourne les rotors jusqu'à ce que la sortie devienne lisible.</div>
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
let indices = 0;

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

// Mémorise le message déchiffré pour le retrouver sur la page d'accueil.
function enregistre(message){
  const store = JSON.parse(localStorage.getItem("poste14:decouvertes") || "{}");
  if (store[INDEX] === message) return;
  store[INDEX] = message;
  localStorage.setItem("poste14:decouvertes", JSON.stringify(store));
  // Signale à l'accueil la découverte fraîche, pour la mettre en avant.
  localStorage.setItem("poste14:dernier", String(INDEX));
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
  moins.onclick = () => tourne(i, -1);
  plus.onclick = () => tourne(i, 1);
  rotors.appendChild(el);
});

function tourne(i, d){
  pos[i] = (pos[i] + d + 26) % 26;
  rendu();
}

let seq = 0;
function rendu(){
  // Déchiffrement local : instantané, aucune requête pendant qu'on tourne.
  pos.forEach((p, i) => {
    document.getElementById("l" + i).textContent = String.fromCharCode(A + p);
    document.getElementById("n" + i).textContent = String(p).padStart(2, "0");
  });
  const sortie = transforme(CIPHER, pos, -1);
  const clair = document.getElementById("clair");
  if (MOT_DONNE && MOT_RE) clair.innerHTML = sortie.replace(MOT_RE, m => `<b>${m}</b>`);
  else clair.textContent = sortie;

  // Victoire détectée en local via l'empreinte, sans exposer le clair.
  const mine = ++seq;
  sha256(sortie).then(h => {
    if (mine !== seq) return;
    const gagne = h === PLAIN_HASH;
    document.getElementById("bande").classList.toggle("gagne", gagne);
    if (gagne) enregistre(sortie);
  });
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

rendu();
</script>
@endpush
