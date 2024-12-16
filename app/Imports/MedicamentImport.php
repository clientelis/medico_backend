<?php

namespace App\Imports;

use App\Models\Medicament;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicamentImport implements ToModel, WithHeadingRow
{
    /**
     * Mappe chaque ligne du fichier CSV vers le modèle Medicament.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Medicament([
            'laboratoire' => $row['laboratoire'] ?? null,
            'nom_produit' => $row['nom_du_produit_presentation'] ?? null,
            'conditionnement' => $row['conditionnement'] ?? null,
            'prix_public' => $row['prix_public_fcfa'] ?? null,
            'pays' => $row['pays'] ?? null,
            'voie_administration' => $row['voie_d_administration'] ?? null,
            'forme' => $row['forme'] ?? null,
            'type' => $row['type'] ?? null,
            'genre' => $row['genre'] ?? null,
            'atc' => $row['atc'] ?? null,
            'dci' => $row['dci_principes_actifs'] ?? null,
            'definition' => $row['definition_produit_classe_therapeutique'] ?? null,
            'conditionnement_detail' => $row['conditionnement'] ?? null,
            'excipients' => $row['excipients'] ?? null,
            'expiration_amm' => $row['expiration_amm'] ?? null,
            'numero_amm' => $row['n_amm'] ?? null,
        ]);
    }
}
