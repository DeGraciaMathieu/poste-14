<?php

namespace App\Http\Controllers;

class PosteController extends Controller
{
    public function index()
    {
        // La liste ne porte que des références : le clair reste secret et n'est
        // révélé que côté client, depuis le localStorage, une fois déchiffré.
        $references = array_map(
            fn ($i) => ['index' => $i, 'ref' => $this->reference($i)],
            array_keys(config('poste.phrases'))
        );

        return view('accueil', ['references' => $references]);
    }

    public function show(int $interception)
    {
        $entree = $this->interception($interception);

        return view('poste', [
            'index' => $interception,
            'ref' => $this->reference($interception),
            // Le chiffré est destiné à être affiché (« Signal capté »).
            'cipher' => $this->transforme($entree['texte'], $entree['cle'], 1),
            // Le clair n'est jamais envoyé : seule son empreinte permet au client
            // de détecter la victoire en local, sans latence.
            'plainHash' => hash('sha256', $entree['texte']),
            'motCle' => $entree['motCle'],
        ]);
    }

    public function hint(int $interception)
    {
        $entree = $this->interception($interception);

        // Indice 2 : révèle la position d'un seul rotor, jamais la clé entière.
        return response()->json(['index' => 0, 'pos' => $entree['cle'][0] ?? 0]);
    }

    private function interception(int $i): array
    {
        return config("poste.phrases.$i") ?? abort(404);
    }

    private function reference(int $i): string
    {
        return sprintf('Interception n°%02d', $i + 1);
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
