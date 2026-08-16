<?php

namespace App\Http\Controllers;

class PosteController extends Controller
{
    public function index()
    {
        $plain = (string) config('poste.plain');
        $cle = (array) config('poste.cle');

        return view('poste', [
            // Le chiffré est destiné à être affiché (« Signal capté »).
            'cipher' => $this->transforme($plain, $cle, 1),
            // Le clair n'est jamais envoyé : seul son empreinte permet au client
            // de détecter la victoire en local, sans latence.
            'plainHash' => hash('sha256', $plain),
        ]);
    }

    public function hint()
    {
        $cle = (array) config('poste.cle');

        // Indice 2 : révèle la position d'un seul rotor, jamais la clé entière.
        return response()->json(['index' => 0, 'pos' => $cle[0] ?? 0]);
    }

    private function transforme(string $txt, array $cle, int $sens): string
    {
        $i = 0;

        return preg_replace_callback('/[A-Z]/', function ($m) use ($cle, $sens, &$i) {
            $d = $cle[$i % count($cle)] * $sens;
            $i++;

            return chr(((ord($m[0]) - 65 + $d) % 26 + 26) % 26 + 65);
        }, $txt);
    }
}
