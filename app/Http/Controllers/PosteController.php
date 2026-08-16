<?php

namespace App\Http\Controllers;

class PosteController extends Controller
{
    public function index()
    {
        // La liste ne porte que des références et le niveau de difficulté : le clair
        // reste secret et n'est révélé que côté client, depuis le localStorage.
        $references = array_map(function ($i) {
            $rotors = count(config("poste.phrases.$i.cle"));

            return [
                'index' => $i,
                'ref' => $this->reference($i),
                'rotors' => $rotors,
                'niveau' => $this->niveau($rotors),
                // Contexte historique révélé à 100 % — jamais le clair.
                'sens' => config("poste.phrases.$i.sens", ''),
            ];
        }, array_keys(config('poste.phrases')));

        return view('accueil', ['references' => $references]);
    }

    public function show(int $interception)
    {
        $entree = $this->interception($interception);
        $aides = $this->aides($entree['cle']);

        return view('poste', [
            'index' => $interception,
            'ref' => $this->reference($interception),
            'rotors' => count($entree['cle']),
            // Le chiffré est destiné à être affiché (« Signal capté »).
            'cipher' => $this->transforme($entree['texte'], $entree['cle'], 1),
            // Le clair n'est jamais envoyé : seule son empreinte permet au client
            // de détecter la victoire en local, sans latence.
            'plainHash' => hash('sha256', $entree['texte']),
            // Le mot n'est livré que si l'aide est active : sinon on trahirait un
            // mot du clair sur un niveau censé n'offrir aucune aide.
            'motDonne' => $aides['mot'],
            'motCle' => $aides['mot'] ? $entree['motCle'] : '',
            'indiceRotor' => $aides['rotor'],
        ]);
    }

    public function hint(int $interception)
    {
        $entree = $this->interception($interception);

        // Pas d'indice de rotor sur les niveaux durs, même en appelant l'URL en direct.
        abort_unless($this->aides($entree['cle'])['rotor'], 404);

        // Indice : révèle la position d'un seul rotor, jamais la clé entière.
        return response()->json(['index' => 0, 'pos' => $entree['cle'][0] ?? 0]);
    }

    // La difficulté monte avec le nombre de rotors, et les aides s'estompent.
    private function aides(array $cle): array
    {
        $rotors = count($cle);

        return [
            'mot' => $rotors <= 4,
            'rotor' => $rotors <= 3,
        ];
    }

    private function interception(int $i): array
    {
        return config("poste.phrases.$i") ?? abort(404);
    }

    private function reference(int $i): string
    {
        return sprintf('Interception n°%02d', $i + 1);
    }

    // Niveau lisible, aligné sur le nombre de rotors (rang de 1 à 4 pour l'affichage).
    private function niveau(int $rotors): array
    {
        return match (true) {
            $rotors <= 3 => ['label' => 'Facile', 'rang' => 1],
            $rotors === 4 => ['label' => 'Moyen', 'rang' => 2],
            $rotors === 5 => ['label' => 'Difficile', 'rang' => 3],
            default => ['label' => 'Expert', 'rang' => 4],
        };
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
