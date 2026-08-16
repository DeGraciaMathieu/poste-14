<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class PosteController extends Controller
{
    public function index()
    {
        $entree = $this->phraseDuJour();

        return view('poste', [
            // Le chiffré est destiné à être affiché (« Signal capté »).
            'cipher' => $this->transforme($entree['texte'], $entree['cle'], 1),
            // Le clair n'est jamais envoyé : seul son empreinte permet au client
            // de détecter la victoire en local, sans latence.
            'plainHash' => hash('sha256', $entree['texte']),
            'motCle' => $entree['motCle'],
        ]);
    }

    public function hint()
    {
        $entree = $this->phraseDuJour();

        // Indice 2 : révèle la position d'un seul rotor, jamais la clé entière.
        return response()->json(['index' => 0, 'pos' => $entree['cle'][0] ?? 0]);
    }

    // Interception du jour, identique pour tous les joueurs, rotation à minuit.
    private function phraseDuJour(): array
    {
        $phrases = config('poste.phrases');
        $jours = Carbon::parse(config('poste.depuis'))->startOfDay()->diffInDays(Carbon::now()->startOfDay());

        return $phrases[$jours % count($phrases)];
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
