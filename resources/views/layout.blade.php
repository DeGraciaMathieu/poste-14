<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', "Station d'écoute — Poste 14")</title>
<style>
  :root{
    --bakelite:#24261F;
    --panneau:#33372E;
    --acier:#585D50;
    --papier:#E9E2CE;
    --encre:#2B2822;
    --laiton:#C4922E;
    --signal:#A8342A;
  }
  *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
  body{
    margin:0;background:var(--bakelite);color:var(--papier);
    font-family:"Helvetica Neue",Arial,sans-serif;
    min-height:100vh;padding:14px 12px 40px;
    background-image:repeating-linear-gradient(0deg,rgba(0,0,0,.16) 0 1px,transparent 1px 4px);
  }
  a{color:inherit;text-decoration:none}
  header{border-bottom:2px solid var(--acier);padding-bottom:8px;margin-bottom:16px}
  .eyebrow{font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:var(--laiton)}
  h1{font-size:15px;letter-spacing:.16em;text-transform:uppercase;margin:4px 0 0;font-weight:600}
  .meta{font-size:10px;letter-spacing:.14em;color:#8B9080;margin-top:3px}

  .poste{max-width:520px;margin:0 auto}
  @media (min-width:640px){
    body{
      padding:48px 20px 64px;
      display:flex;justify-content:center;align-items:flex-start;
    }
    .poste{
      background:linear-gradient(180deg,var(--panneau),#2A2D26);
      padding:30px 34px 36px;border-radius:10px;
      border:1px solid var(--acier);
      box-shadow:0 22px 55px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.05);
    }
    h1{font-size:17px}
  }
</style>
@stack('styles')
</head>
<body>
<main class="poste">
@yield('content')
</main>
@stack('scripts')
</body>
</html>
