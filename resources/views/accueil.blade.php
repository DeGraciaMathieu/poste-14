@extends('layout')

@section('title', 'Interceptions — Poste 14')

@push('styles')
<style>
  .progression{height:4px;background:rgba(255,255,255,.08);border-radius:2px;margin-top:10px;overflow:hidden}
  .progression > i{display:block;height:100%;width:0;background:var(--laiton);transition:width .6s cubic-bezier(.2,.9,.3,1)}
  .complete{
    display:none;margin-top:12px;padding:10px 12px;border:1px solid var(--laiton);border-radius:4px;
    color:var(--laiton);font-size:10px;letter-spacing:.2em;text-transform:uppercase;text-align:center;
    background:rgba(196,146,46,.08);
  }
  .complete.on{display:block}

  .liste{list-style:none;margin:16px 0 0;padding:0;display:flex;flex-direction:column;gap:8px}
  .item{
    position:relative;overflow:hidden;display:block;
    background:var(--panneau);border:1px solid var(--acier);border-radius:5px;padding:12px 14px;
  }
  .item:active{transform:translateY(1px)}
  .item .ref{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--laiton)}

  .niveau{display:flex;align-items:center;gap:6px;margin-top:5px;font-size:9px;letter-spacing:.14em;text-transform:uppercase}
  .niveau .pips{display:inline-flex;gap:3px}
  .niveau .pips i{width:6px;height:6px;border-radius:50%;background:currentColor;opacity:.25}
  .niveau .pips i.on{opacity:1}
  .niveau .rotors{color:#7E8371;letter-spacing:.1em}
  .niveau.n1{color:#8FA98A}
  .niveau.n2{color:var(--laiton)}
  .niveau.n3{color:#D08A3E}
  .niveau.n4{color:var(--signal)}

  .item .etat{
    font-family:"Courier New",monospace;font-size:14px;color:#7E8371;
    letter-spacing:.08em;margin-top:8px;word-break:break-word;line-height:1.6;
  }
  .item .fleche{position:absolute;right:12px;top:12px;color:#7E8371;font-size:18px;line-height:1}

  .tampon-liste{
    position:absolute;right:10px;top:14px;border:2px solid var(--signal);color:var(--signal);
    font-size:9px;letter-spacing:.18em;text-transform:uppercase;font-weight:700;padding:2px 6px;border-radius:2px;
    transform:rotate(-8deg);opacity:0;pointer-events:none;
  }

  .item.trouvee{background:var(--papier);border-color:var(--papier)}
  .item.trouvee .ref{color:#8A8069}
  .item.trouvee .etat{color:var(--encre);font-weight:700}
  .item.trouvee .fleche{display:none}
  .item.trouvee .tampon-liste{opacity:.85}

  @keyframes pop{0%{transform:scale(.97);opacity:.5}60%{transform:scale(1.015)}100%{transform:scale(1);opacity:1}}
  @keyframes tamponIn{0%{opacity:0;transform:rotate(-8deg) scale(2.4)}100%{opacity:.85;transform:rotate(-8deg) scale(1)}}
  .item.fraiche{animation:pop .5s cubic-bezier(.2,1.3,.4,1)}
  .item.fraiche .tampon-liste{animation:tamponIn .5s cubic-bezier(.2,1.3,.4,1) both}

  @media (prefers-reduced-motion:reduce){
    .item.fraiche,.item.fraiche .tampon-liste{animation:none}
    .progression > i{transition:none}
  }
</style>
@endpush

@section('content')
<header>
  <div class="eyebrow">Station d'écoute</div>
  <h1>Poste 14 — Interceptions</h1>
  <div class="meta" id="compteur">— / {{ count($references) }} déchiffrées</div>
  <div class="progression"><i id="jauge"></i></div>
</header>

<div class="complete" id="complete">Transmission complète — toutes les interceptions déchiffrées</div>

<ul class="liste">
  @foreach ($references as $r)
    <li>
      <a class="item" data-i="{{ $r['index'] }}" href="/interception/{{ $r['index'] }}">
        <span class="fleche">›</span>
        <span class="tampon-liste">Déchiffrée</span>
        <div class="ref">{{ $r['ref'] }}</div>
        <div class="niveau n{{ $r['niveau']['rang'] }}">
          <span>{{ $r['niveau']['label'] }}</span>
          <span class="pips">@for ($p = 1; $p <= 4; $p++)<i class="{{ $p <= $r['niveau']['rang'] ? 'on' : '' }}"></i>@endfor</span>
          <span class="rotors">· {{ $r['rotors'] }} rotors</span>
        </div>
        <div class="etat">— non déchiffrée —</div>
      </a>
    </li>
  @endforeach
</ul>
@endsection

@push('scripts')
<script>
const store = JSON.parse(localStorage.getItem("poste14:decouvertes") || "{}");
// Marqueur posé par la dernière interception résolue, pour l'animer une seule fois.
const dernier = localStorage.getItem("poste14:dernier");
localStorage.removeItem("poste14:dernier");

const items = document.querySelectorAll(".item");
let trouvees = 0;

items.forEach(item => {
  const message = store[item.dataset.i];
  if (message){
    trouvees++;
    item.classList.add("trouvee");
    item.querySelector(".etat").textContent = message;
    if (item.dataset.i === dernier) item.classList.add("fraiche");
  }
});

const total = items.length;
document.getElementById("compteur").textContent = trouvees + " / " + total + " déchiffrées";
document.getElementById("jauge").style.width = (total ? trouvees / total * 100 : 0) + "%";
if (total > 0 && trouvees === total) document.getElementById("complete").classList.add("on");
</script>
@endpush
