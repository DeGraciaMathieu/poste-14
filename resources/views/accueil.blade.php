@extends('layout')

@section('title', 'Interceptions — Poste 14')

@push('styles')
<style>
  .liste{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px}
  .item{
    display:block;background:var(--panneau);border:1px solid var(--acier);
    border-radius:5px;padding:12px 14px;
  }
  .item:active{transform:translateY(1px)}
  .item .ref{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--laiton)}
  .item .etat{
    font-family:"Courier New",monospace;font-size:14px;color:#7E8371;
    letter-spacing:.08em;margin-top:6px;word-break:break-word;line-height:1.6;
  }
  .item .fleche{float:right;color:#7E8371;font-size:18px;line-height:1}
  .item.trouvee{background:var(--papier);border-color:var(--papier)}
  .item.trouvee .ref{color:#8A8069}
  .item.trouvee .etat{color:var(--encre);font-weight:700}
  .item.trouvee .fleche{color:#8A8069}
</style>
@endpush

@section('content')
<header>
  <div class="eyebrow">Station d'écoute</div>
  <h1>Poste 14 — Interceptions</h1>
  <div class="meta" id="compteur">— / {{ count($references) }} déchiffrées</div>
</header>

<ul class="liste">
  @foreach ($references as $r)
    <li>
      <a class="item" data-i="{{ $r['index'] }}" href="/interception/{{ $r['index'] }}">
        <span class="fleche">›</span>
        <div class="ref">{{ $r['ref'] }}</div>
        <div class="etat">— non déchiffrée —</div>
      </a>
    </li>
  @endforeach
</ul>
@endsection

@push('scripts')
<script>
const store = JSON.parse(localStorage.getItem("poste14:decouvertes") || "{}");
const items = document.querySelectorAll(".item");
let trouvees = 0;

items.forEach(item => {
  const message = store[item.dataset.i];
  if (message){
    trouvees++;
    item.classList.add("trouvee");
    item.querySelector(".etat").textContent = message;
  }
});

document.getElementById("compteur").textContent = trouvees + " / " + items.length + " déchiffrées";
</script>
@endpush
